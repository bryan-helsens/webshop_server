<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductListResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 9);
        $search = request('search', '');
        $category_slug = request('category_slug', 'all');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $categoryID = null;

        if ($category_slug !== null && $category_slug !== "all") {
            $category = Category::where('slug', $category_slug)->first();
            $categoryID = $category->id;
        }

        $products = Product::with('category')
            ->where('name', 'like', "%{$search}%")
            ->where(function ($query) use ($categoryID) {
                if ($categoryID !== null) {
                    return $query->where('category_id', $categoryID);
                }
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return ProductListResource::collection($products);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        $image = $data['image'] ?? null;
        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = URL::to(Storage::url($relativePath));
        }

        if ($data) {
            $product = Product::create($data);
        }

        return new ProductResource($product);
    }

    public function show($id)
    {
        $product = Product::find($id);
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, $id)
    {
        $data = $request->validated();
        $product = Product::find($id);

        $image = $data['image'] ?? null;
        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = URL::to(Storage::url($relativePath));

            if ($product->image) {
                Storage::deleteDirectory('/public/' . dirname($product->image));
            }
        }

        if ($data) {
            $product->update($data);
        }

        return new ProductResource($product);;
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return response()->noContent();
    }

    private function saveImage(UploadedFile $image)
    {
        $path = 'images/' . Str::random();
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }

        if (!Storage::putFileAs('public/' . $path, $image, $image->getClientOriginalName())) {
            throw new Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
        }

        return $path . '/' . $image->getClientOriginalName();
    }
}
