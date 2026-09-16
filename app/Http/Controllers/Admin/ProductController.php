<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function create(): View
    {
        return view('admin.catalog.product-form');
    }

    public function index(): View
    {
        return view('admin.catalog.products');
    }

    public function edit(Product $product): View
    {
        return view('admin.catalog.product-form', compact('product'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'primaryImage', 'inventory']);

        return view('admin.catalog.product-detail', compact('product'));
    }
}
