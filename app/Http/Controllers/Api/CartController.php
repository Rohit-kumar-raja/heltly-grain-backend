<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get the current user's cart
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = Cart::with(['items.product'])->firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active']
        );

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        $user = $request->user();
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active']
        );

        $item = $cart->items()->where('product_id', $validated['product_id'])->first();

        if ($item) {
            $item->quantity += $validated['quantity'] ?? 1;
            $item->save();
        } else {
            $item = $cart->items()->create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'] ?? 1
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $cart->load('items.product')
        ]);
    }

    /**
     * Update item quantity
     */
    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->where('status', 'active')->firstOrFail();

        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->quantity = $validated['quantity'];
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'data' => $cart->load('items.product')
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, $itemId)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->where('status', 'active')->firstOrFail();

        $cart->items()->where('id', $itemId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => $cart->load('items.product')
        ]);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->where('status', 'active')->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'data' => $cart ? $cart->load('items.product') : null
        ]);
    }
}
