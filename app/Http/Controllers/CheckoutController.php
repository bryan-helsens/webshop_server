<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $TAX = 0.07;

    public function __construct()
    {
        $this->middleware('jwtauth');
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
                'data.userInfo.shippingAddress.street2' => 'nullable|string',
                'data.userInfo.shippingAddress.city' => 'required|string',
                'data.userInfo.shippingAddress.state' => 'required|string',
                'data.userInfo.shippingAddress.zipCode' => 'required|string',
                'data.userInfo.shippingAddress.isSameAddress' => 'required|boolean',
            ]);

            $shippingAddress = $shippingAddress["data"]["userInfo"]["shippingAddress"];
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
            'user_id' => Auth::id(),
            'payment_id' => "COD",
            'subtotal' => $subtotal,
            'tax_price' => $tax_price,
            'total_price' => $total_price,
        ]);

        $orderBillingAddress[] = [
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
            'type' => 0,
        ];

        if (!$isAddressSame) {
            $orderShippingAddress[] = [
                'firstname' => $shippingAddress['firstName'],
                'lastname' => $shippingAddress['lastName'],
                'phone' => $personalData['phoneNumber'],
                'email' => $personalData['email'],
                'street1' => $shippingAddress['street1'],
                'street2' => $shippingAddress['street2'],
                'city' => $shippingAddress['city'],
                'country' => $shippingAddress['country'],
                'state' => $shippingAddress['state'],
                'zipcode' => $shippingAddress['zipCode'],
                'type' => 1,
            ];
        }


        $order->orderItems()->createMany($orderItems);

        $order->orderBillingAddress()->create($orderBillingAddress[0]);

        if (!$isAddressSame) {
            $order->orderShippingAddress()->create($orderShippingAddress[0]);
        }


        return response()->json([
            'status' => 'success',
            'message' =>  'Order ' . $order["order_id"] . ' Placed Successfully'
        ]);
    }
}
