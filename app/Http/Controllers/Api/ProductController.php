<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category !== 'All') {
            $category = $request->category;
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        return $query->paginate(10);
    }

    public function show($id)
    {
        return Product::with('category')->findOrFail($id);
    }
}
