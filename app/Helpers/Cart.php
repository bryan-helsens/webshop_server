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

            unset($dbCartItems[$cartItem['id']]);
        }

        if (!empty($newCartItems)) {
            CartItem::insert($newCartItems);
        }

        if (!empty($dbCartItems)) {
            foreach ($dbCartItems as $dbItem) {
                $dbItem->delete();
            }
        }
    }
}
