<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class WishlistController extends Controller
{
    public function index()
    {
        return Inertia::render('Wishlists/Index');
    }

    public function getData()
    {
        return DataTables::of(
            Wishlist::with(['user:id,name,email', 'product:id,name,price,image'])
        )
            ->addColumn('user_name', fn($w) => $w->user?->name ?? 'N/A')
            ->addColumn('user_email', fn($w) => $w->user?->email ?? 'N/A')
            ->addColumn('product_name', fn($w) => $w->product?->name ?? 'N/A')
            ->addColumn('product_price', fn($w) => $w->product?->price ?? 0)
            ->addColumn('product_image', fn($w) => $w->product?->image ?? '')
            ->toJson();
    }

    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();
        return redirect()->back()->with('success', 'Wishlist item deleted successfully.');
    }

    /**
     * Get wishlist statistics for dashboard
     */
    public function stats()
    {
        return response()->json([
            'total' => Wishlist::count(),
            'today' => Wishlist::whereDate('created_at', today())->count(),
            'top_products' => Wishlist::selectRaw('product_id, COUNT(*) as count')
                ->with('product:id,name,image')
                ->groupBy('product_id')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ]);
    }
}
