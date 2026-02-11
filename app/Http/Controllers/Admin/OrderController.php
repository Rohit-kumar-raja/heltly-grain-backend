<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Orders/Index');
    }

    public function getData()
    {
        return DataTables::of(Order::with('user'))->toJson();
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        // Sync transaction status with order status
        $transaction = Transaction::where('order_id', $order->id)->latest()->first();
        if ($transaction) {
            $txnStatus = match ($validated['status']) {
                'delivered' => 'success',
                'cancelled' => 'failed',
                default => 'pending',
            };
            $transaction->update(['status' => $txnStatus]);
        }

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
