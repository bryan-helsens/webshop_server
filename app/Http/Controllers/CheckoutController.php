<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class CheckoutController extends Controller
{
    protected $TAX = 0.07;

    public function __construct()
    {
        //$this->middleware('jwtauth');
    }


    public function createOrder(Request $request)
    {
        $billingAddress = $request->validate([
            'data.userInfo.billingAddress.firstName' => 'required|string',
            'data.userInfo.billingAddress.lastName' => 'required|string',
            'data.userInfo.billingAddress.country' => 'required|string',
            'data.userInfo.billingAddress.street1' => 'required|string',
            'data.userInfo.billingAddress.street2' => 'nullable|string',
            'data.userInfo.billingAddress.city' => 'required|string',
            'data.userInfo.billingAddress.state' => 'required|string',
            'data.userInfo.billingAddress.zipCode' => 'required|string',
        ]);

        $isAddressSame = $request["data"]["userInfo"]["shippingAddress"]["isSameAddress"];

        if (!$isAddressSame) {
            $shippingAddress = $request->validate([
                'data.userInfo.shippingAddress.firstName' => 'required|string',
                'data.userInfo.shippingAddress.lastName' => 'required|string',
                'data.userInfo.shippingAddress.country' => 'required|string',
                'data.userInfo.shippingAddress.street1' => 'required|string',
                'data.userInfo.shippingAddress.city' => 'required|string',
                'data.userInfo.shippingAddress.state' => 'required|string',
                'data.userInfo.shippingAddress.zipCode' => 'required|string',
                'data.userInfo.shippingAddress.isSameAddress' => 'required|boolean',
            ]);
        }

        $personalData = $request->validate([
            'data.userInfo.email' => 'required|string|email',
            'data.userInfo.phoneNumber' => 'required|string',
        ]);

        $billingAddress = $billingAddress["data"]["userInfo"]["billingAddress"];
        $personalData = $personalData["data"]["userInfo"];
        $cartProducts = $request["data"]["cart"];

        $orderItems = [];
        $subtotal = 0;
        foreach ($cartProducts as $product) {
            $id = $product["id"];
            $qty = $product["count"];

            $productByID = Product::where('id', $id)->first();

            $orderItems[] = [
                "product_id" => $productByID->id,
                "qty" => $qty,
                "price" => (int)$productByID->price,
                "total_price" => $productByID->price * $qty,
            ];

            $productByID->quantity -= $qty;
            $productByID->save();

            $subtotal += $productByID->price * $qty;
        }

        $subtotal = number_format((float)$subtotal, 2, ".", "");
        $tax_price = number_format((float)($subtotal * $this->TAX), 2, ".", "");
        $total_price = $subtotal + $tax_price;

        $order = Order::Create([
            'order_id' => uniqid('ORD.'),
            'firstname' => $billingAddress['firstName'],
            'lastname' => $billingAddress['lastName'],
            'phone' => $personalData['phoneNumber'],
            'email' => $personalData['email'],
            'street1' => $billingAddress['street1'],
            'street2' => $billingAddress['street2'],
            'city' => $billingAddress['city'],
            'country' => $billingAddress['country'],
            'state' => $billingAddress['state'],
            'zipcode' => $billingAddress['zipCode'],
            'payment_id' => "COD",
            'subtotal' => $subtotal,
            'tax_price' => $tax_price,
            'total_price' => $total_price,
        ]);

        $order->orderItems()->createMany($orderItems);

        return response()->json([
            'status' => 'success',
            'message' =>  'Order ' . $order["order_id"] . ' Placed Successfully'
        ]);
    }
}
