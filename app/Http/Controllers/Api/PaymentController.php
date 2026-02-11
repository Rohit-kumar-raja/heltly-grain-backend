<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initiate a payment transaction.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'gateway' => 'required|string|in:razorpay,cod', // scalable
        ]);

        $user = $request->user();
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if already paid
        if ($order->status === 'paid' || $order->status === 'processing') {
            return response()->json(['message' => 'Order already paid'], 400);
        }

        // Create Transaction record
        // For COD, we might just update valid immediately, but usually client confirms.
        // For Razorpay, we generate an Order ID from Razorpay API.

        // Mocking Gateway ID generation for now
        $gatewayId = 'pay_' . Str::random(10);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'transaction_id' => $gatewayId, // This would be 'order_...' for Razorpay Orders API
            'gateway' => $request->gateway,
            'amount' => $order->total,
            'status' => 'pending',
            'currency' => 'INR',
        ]);

        return response()->json([
            'transaction_id' => $transaction->transaction_id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'key' => env('RAZORPAY_KEY'), // mocked
        ]);
    }

    /**
     * Verify payment status.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|string|in:success,failed',
            'response' => 'nullable|array', // Gateway response payload
        ]);

        $transaction = Transaction::where('transaction_id', $request->transaction_id)->firstOrFail();

        if ($transaction->status === 'success') {
            return response()->json(['message' => 'Transaction already verified']);
        }

        DB::beginTransaction();
        try {
            $transaction->update([
                'status' => $request->status,
                'response' => $request->response,
                'payment_method' => $request->response['method'] ?? 'unknown',
            ]);

            if ($request->status === 'success') {
                $order = Order::find($transaction->order_id);
                if ($order) {
                    $order->update([
                        'status' => 'processing', // or paid
                        'payment_method' => $transaction->gateway,
                        'payment_id' => $transaction->transaction_id,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Payment status updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Verification failed', 'error' => $e->getMessage()], 500);
        }
    }
}
