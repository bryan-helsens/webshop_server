<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\Customer;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwtauth');
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;
        $customer->email = $user->email;

        return response()->json([
            'status' => 'success',
            'customer' => $customer,
        ]);
    }

    public function store(ProfileRequest $request)
    {
        $customerData = $request->validated();
        $user = $request->user();
        $customer = $user->customer;

        $customer->update($customerData["customer"]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile was successfully updated.',
        ]);
    }
}
