<?php

namespace App\Helper;

use App\Models\CartItem;

class Cart
{
    public static function getCartItemsCount(): int
    {
        $request = request();
        $user = $request->user;

        if ($user) {
            return CartItem::where('user_id', $user["id"])->sum('quantity');
        } else {
            $cartItems = Self::getCookieCartItems();

            return array_reduce(
                $cartItems,
                fn ($carry, $item) => $carry + $item['quantity'],
                0
            );
        }
    }

    public static function getCartItems($user)
    {
        if ($user) {
            $cart = CartItem::where('user_id', $user["id"])->get()->map(
                fn ($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity]
            )->keyBy('product_id');

            return $cart !== null ? $cart : "[]";
        } else {
            return Self::getCookieCartItems();
        }
    }

    public static function getCookieCartItems()
    {
        $request = request();

        return json_decode($request->cookies->get('cart_items', '[]'), true);
    }

    public static function getCountFromItems($cartItems)
    {
        return array_reduce(
            $cartItems,
            fn ($carry, $item) => $carry + $item['quantity'],
            0
        );
    }

    public static function moveCartItemsIntoDB($user_id)
    {
        $request = request();
        $cartItems = Self::getCookieCartItems();
        $dbCartItems = CartItem::where(['user_id' => $user_id])->get()->keyBy('product_id');

        $newCartItems = [];
        foreach ($cartItems as $cartItem) {
            if (isset($dbCartItems[$cartItem['product_id']])) {
                continue;
            }

            $newCartItems[] = [
                "user_id" => $request->user()->id,
                "product_id" => $cartItem['product_id'],
                "quantity" => $cartItem['quantity'],
            ];
        }

        if (!empty($newCartItems)) {
            CartItem::insert($newCartItems);
        }
    }
}
