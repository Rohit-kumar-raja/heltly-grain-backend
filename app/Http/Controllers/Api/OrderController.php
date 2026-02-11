<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Order::where('user_id', $request->user()->id)->with('items.product')->latest()->get();
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'items' => 'required|array',
            'subtotal' => 'required|numeric',
            'shipping' => 'required|numeric',
            'total' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_id' => 'nullable|string',
            'address' => 'required|array',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
            'subtotal' => $validated['subtotal'],
            'shipping' => $validated['shipping'],
            'total' => $validated['total'],
            'payment_method' => $validated['payment_method'],
            'payment_id' => $validated['payment_id'] ?? null,
            'address' => $validated['address'],
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $order->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
        }

        // Create a Transaction record for every order
        $isCod = $validated['payment_method'] === 'cod';
        Transaction::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'transaction_id' => $isCod
                ? 'COD-' . strtoupper(Str::random(10))
                : ($validated['payment_id'] ?? 'PAY-' . strtoupper(Str::random(10))),
            'gateway' => $validated['payment_method'],
            'amount' => $order->total,
            'currency' => 'INR',
            'status' => $isCod ? 'pending' : 'pending',
            'payment_method' => $validated['payment_method'],
            'response' => $isCod ? ['method' => 'cod', 'note' => 'Cash on Delivery'] : null,
        ]);

        // Notify Admin
        \App\Models\AdminNotification::orderPlaced($order, $user->name);

        return $order->load('items');
    }
    public function show(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('order_number', $id);
            })
            ->with('items.product')
            ->firstOrFail();

        return $order;
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('order_number', $id);
            })
            ->firstOrFail();

        if ($order->status === 'delivered' || $order->status === 'cancelled') {
            return response()->json(['message' => 'Order cannot be cancelled'], 400);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json(['message' => 'Order cancelled successfully', 'order' => $order]);
    }
}
