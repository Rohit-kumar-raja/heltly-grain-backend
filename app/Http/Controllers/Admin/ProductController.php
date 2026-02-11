<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::select('id', 'name')->get();
        return Inertia::render('Products/Index', [
            'categories' => $categories
        ]);
    }

    public function getData()
    {
        return DataTables::of(Product::with('category')->select('products.*'))
            ->addColumn('category_name', function ($product) {
                return $product->category ? $product->category->name : '-';
            })
            ->toJson();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'pack' => 'nullable|string',
            'benefits' => 'nullable|string', // Keep as string or array? Model casts to array. If frontend sends JSON string or array?
            // Let's assume frontend sends array or we handle it. 
            // If model casts to array, Laravel expects array or JSON string.
            // Let's allow nullable array or string for benefits to be safe if client sends JSON string
            'benefits' => 'nullable',
            'image' => 'nullable|image|max:1024',
            'category_id' => 'nullable|exists:categories,id',
            'rating' => 'nullable|numeric|min:0|max:5',
            'calories' => 'nullable|integer|min:0',
            'nutrition' => 'nullable|array', // Dynamic key-value
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = Storage::url($path);
        }

        Product::create($validated);

        return redirect()->back()->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'pack' => 'nullable|string',
            'benefits' => 'nullable',
            'image' => 'nullable|image|max:1024',
            'category_id' => 'nullable|exists:categories,id',
            'rating' => 'nullable|numeric|min:0|max:5',
            'calories' => 'nullable|integer|min:0',
            'nutrition' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image code...
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = Storage::url($path);
        }

        $product->update($validated);

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
