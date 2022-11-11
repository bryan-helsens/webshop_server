<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public $pageSize = 6;
    public $category_slug = "all";

    public function index(Request $request)
    {
        $products_query = Product::with('category');
        $category_slug = $request->category_slug;
        $search = $request->search;


        if ($search !== '' && $category_slug !== '' && $category_slug !== "all") {
            $category = Category::where('slug', $category_slug)->first();
            $category_id = $category->id;
            $products_query->where('category_id', $category_id)->where('name', 'like', '%' . $search . '%')->get();
        } else if ($category_slug !== null && $category_slug !== "all") {
            $category = Category::where('slug', $category_slug)->first();
            $category_id = $category->id;
            $products_query->where('category_id', $category_id);
        } else if ($search !== '') {
            $products_query->where('name', 'like', '%' . $search . '%')->get();
        }

        $products = $products_query->paginate($this->pageSize);
        return $products;
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => 'required',
            "slug" => 'required',
            'price' => 'required',
        ]);

        return Product::create($request->all());
    }

    public function show($id)
    {
        return Product::with('category')->find($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return $product;
    }

    public function destroy($id)
    {
        return Product::destroy($id);
    }
}
