<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Cart;
use App\Http\Resources\ProductCartResource;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwtauth');
    }

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
            $product['max_qty'] =  $product['quantity'];
            $product['quantity'] = $cartItems[$product->id]['quantity'];
            $total += (int)$product->price * $cartItems[$product->id]['quantity'];
        }

        return response()->json([
            'status' => 'success',
            'empty' => false,
            'cartItems' => ProductCartResource::collection($products),
            'total' => $total
        ]);
    }


    public function add(Request $request, Product $product)
    {
        $quantity = $request->get('quantity', 1);
        $user = $request->user;

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
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong!',
        ], Response::HTTP_NOT_FOUND);
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
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong!',
        ], Response::HTTP_NOT_FOUND);
    }

    public function updateQuantity(Request $request, Product $product)
    {
        $quantity = $request->get('quantity', 1);
        $user = $request->user;

        if ($user && $quantity > 0 && $quantity <= $product->quantity) {
            CartItem::where(['user_id' => $user["id"], 'product_id' => $product->id])->update(['quantity' => $quantity]);

            return response()->json([
                'status' => 'success',
                'count' => Cart::getCartItemsCount(),
            ]);
        }
    }

    public function updateCart(Request $request)
    {
        $cartItems = $request->get('cartItems');
        $user = $request->user;

        if ($user && $cartItems) {
            Cart::moveCartItemsIntoDB($user["id"], $cartItems);
        }

        $cartData = Cart::getCartItemsWithProduct($user);

        return response()->json([
            'status' => 'success',
            'empty' => false,
            'cartItems' => ProductCartResource::collection($cartData["cartItems"]),
            'total' => $cartData["total"],
        ]);
    }
}
