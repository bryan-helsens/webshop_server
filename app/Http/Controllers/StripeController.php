<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        $YOUR_DOMAIN = 'http://localhost:8000/checkout';

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[  
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'T-shirt',
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost:4242/success',
            'cancel_url' => 'http://localhost:4242/cancel',
        ]);

        return redirect($checkout_session->url);
    }
}
