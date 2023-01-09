<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::getCartItems($request->user);

        if (!count($cartItems)) {
            return response()->json([
                'status' => 'success',
                'empty' => true,
                'message' => "Cart is empty",
            ]);
        }

        $ids = Arr::pluck($cartItems, "product_id");
        $products = Product::query()->whereIn('id', $ids)->get();
        $total = 0;

        foreach ($products as $product) {
            $total += (int)$product->price * $cartItems[$product->id]['quantity'];
        }

        return response()->json([
            'status' => 'success',
            'cartItems' => $cartItems,
            'products' => $products,
            'total' => $total
        ]);
    }


    public function add(Request $request, Product $product)
    {
        $quantity = $request->get('quantity', 1);
        $user = Auth::user();

        dd($user);

        if ($user) {
            $cartItem = CartItem::where(['user_id' => $user["id"], 'product_id' => $product->id])->first();

            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->update();
            } else {
                $data = [
                    'user_id' => $request->user["id"],
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ];

                CartItem::create($data);
            }

            return response()->json([
                'status' => 'success',
                'count' => Cart::getCartItemsCount(),
            ]);
        } else {
            $cartItems = Cart::getCookieCartItems();
            $productFound = false;

            foreach ($cartItems as &$item) {
                if ($item["quantity"] === $product->id) {
                    $item['quantity'] = $quantity;
                    $productFound = true;
                    break;
                }
            }

            if (!$productFound) {
                $cartItems[] = [
                    "user_id" => null,
                    "product_id" => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ];
            }

            //Cookie::queue('cart_items', json_decode($cartItems), 60 * 24 * 30);

            return response()->json([
                'status' => 'success',
                'count' => Cart::getCountFromItems($cartItems),
            ]);
        }
    }


    public function remove(Request $request, Product $product)
    {
        $user = $request->user;

        if ($user) {
            $cartItem = CartItem::where(['user_id' => $user["id"], 'product_id' => $product->id])->first();

            if ($cartItem) {
                $cartItem->delete();
            }

            return response()->json([
                'status' => 'success',
                'count' => Cart::getCartItemsCount(),
            ]);
        } else {
            $cartItems = Cart::getCookieCartItems();

            foreach ($cartItems as $key => &$item) {
                if ($item['product_id'] === $product->id) {
                    array_splice($cartItems, $key, 1);
                    break;
                }
            }

            Cookie::queue('cart_items', json_decode($cartItems), 60 * 24 * 30);

            return response()->json([
                'status' => 'success',
                'count' => Cart::getCountFromItems($cartItems),
            ]);
        }
    }
}
