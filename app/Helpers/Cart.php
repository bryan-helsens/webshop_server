<?php

namespace App\Helper;

use App\Models\CartItem;
use Illuminate\Support\Arr;
use App\Models\Product;

class Cart
{
    public static function getCartItemsCount(): int
    {
        $request = request();
        $user = $request->user;

        if ($user) {
            return CartItem::where('user_id', $user["id"])->sum('quantity');
        }

        return null;
    }

    public static function getCartItems($user)
    {
        if ($user) {
            $cart = CartItem::where('user_id', $user["id"])->get()->map(
                fn ($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity]
            )->keyBy('product_id');

            return $cart !== null ? $cart : "[]";
        }

        return null;
    }

    public static function getCartItemsWithProduct($user)
    {
        if ($user) {
            $cartItems = Cart::getCartItems($user);

            if (!count($cartItems)) {
                return null;
            }

            $ids = Arr::pluck($cartItems, "product_id");
            $products = Product::query()->whereIn('id', $ids)->get();
            $total = 0;

            foreach ($products as $product) {
                $product['max_qty'] =  $product['quantity'];
                $product['quantity'] = $cartItems[$product->id]['quantity'];
                $total += (int)$product->price * $cartItems[$product->id]['quantity'];
            }
        }

        return ['cartItems' => $products, 'total' => $total];
    }

    public static function moveCartItemsIntoDB($user_id, $cartItems)
    {
        $request = request();
        $dbCartItems = CartItem::where(['user_id' => $user_id])->get()->keyBy('product_id');

        $newCartItems = [];
        foreach ($cartItems as $cartItem) {

            if (isset($dbCartItems[$cartItem['id']]) && $cartItem['quantity'] !== $dbCartItems[$cartItem['id']]["quantity"]) {
                CartItem::where(['user_id' => $user_id, 'product_id' => $cartItem["id"]])->update(['quantity' => $cartItem['quantity']]);
            }

            if (!isset($dbCartItems[$cartItem['id']])) {
                $newCartItems[] = [
                    "user_id" => $request->user()->id,
                    "product_id" => $cartItem['id'],
                    "quantity" => $cartItem['quantity'],
                    "created_at" => \Carbon\Carbon::now()->toDateTimeString(),
                    "updated_at" => \Carbon\Carbon::now()->toDateTimeString(),
                ];
            }
        }

        if (!empty($newCartItems)) {
            CartItem::insert($newCartItems);
        }
    }
}
