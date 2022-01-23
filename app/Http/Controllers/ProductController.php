<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::when(request('search'), function ($query) {
            $query->where('name', 'like', '%' . request('search') . '%');
        })->latest()->paginate(3);
    }

    public function create()
    {
        //
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        return $product;
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update([
            'name' => $request->name,
            'detail' => $request->detail
        ]);

        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
    }
}
// YTP - 31200
// 5BB - 
// Hi Internet
// MyanmarNet (over 30000ks)
// MBT 