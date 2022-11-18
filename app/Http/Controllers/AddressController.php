<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class AddressController extends Controller
{
    public function __construct()
    {
        //$this->middleware('jwtauth');
    }

    public function all()
    {
        $addresses = User::find(Auth::id())->addresses;

        if ($addresses->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'message' => "No addresses found!",
            ]);
        }

        return response()->json([
            'status' => 'success',
            'addresses' => $addresses,
        ]);
    }

    public function getByID($id)
    {
        $addresses = Address::find($id);

        if (!$addresses) {
            return response()->json([
                'status' => 'failed',
                'message' => "No addresses found!",
            ]);
        }

        return response()->json([
            'status' => 'success',
            'addresses' => $addresses,
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'title' => 'min:2|string|max:5|required',
            'firstName' => 'min:3|string|max:255|required',
            'lastName' => 'min:3|string|max:255|required',
            'street' => 'min:3|string|max:255|required',
            'number' => 'int|required',
            'city' => 'min:3|string|max:255|required',
            'country' => 'min:3|string|max:255|required',
            'zipcode' => 'int|required',
        ]);

        $address = [
            "title" => $request->title,
            "firstName" => $request->firstName,
            "lastName" => $request->lastName,
            "street" => $request->street,
            "number" => $request->number,
            "city" => $request->city,
            "country" => $request->country,
            "zipcode" => $request->zipcode,
        ];

        $address = Address::create($address);

        $user = User::find(Auth::id());
        $user->addresses()->attach($address->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully added an address',
            'addresses' => $address,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find(Auth::id());

            $check = $user->addresses()->where('addresses.id', $id)->exists();
            if ($check) {
                $address = Address::find($id);

                $addressData = [
                    "title" => $request->title,
                    "firstName" => $request->firstName,
                    "lastName" => $request->lastName,
                    "street" => $request->street,
                    "number" => $request->number,
                    "city" => $request->city,
                    "country" => $request->country,
                    "zipcode" => $request->zipcode,
                ];

                $address->update($addressData);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully updated your address',
                    'addresses' => $address,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => "Can't update address",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find(Auth::id());

            $check = $user->addresses()->where('addresses.id', $id)->exists();
            if ($check) {
                $user->addresses()->detach($id);
                Address::destroy($id);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully deleted your address',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No address has been found',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }
}
