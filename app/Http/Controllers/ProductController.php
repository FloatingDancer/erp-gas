<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        ActivityLog::log('Create', 'Menambahkan produk baru: ' . $product->name . ' (Stok: ' . $product->stock . ')');

        return redirect()->route('products.index')
                         ->with('success', 'Product added successfully');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        ActivityLog::log('Update', 'Memperbarui produk: ' . $product->name . ' (Stok baru: ' . $product->stock . ')');

        return redirect()->route('products.index')
                         ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        ActivityLog::log('Delete', 'Menghapus produk: ' . $name);

        return redirect('/products')->with('success', 'Product deleted');
    }
}