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
        $this->middleware('jwtauth');
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
            'address.title' => 'nullable',
            'address.firstName' => 'min:3|string|max:255|required',
            'address.lastName' => 'min:3|string|max:255|required',
            'address.street' => 'min:3|string|max:255|required',
            'address.number' => 'int|required',
            'address.city' => 'min:3|string|max:255|required',
            'address.country' => 'min:3|string|max:255|required',
            'address.zipCode' => 'int|required',
            'address.billing_address' => 'boolean',
            'address.shipping_address' => 'boolean',
        ]);

        $address = [
            "title" => $request->address["title"],
            "firstName" => $request->address["firstName"],
            "lastName" => $request->address["lastName"],
            "street" => $request->address["street"],
            "number" => $request->address["number"],
            "city" => $request->address["city"],
            "country" => $request->address["country"],
            "zipcode" => $request->address["zipCode"],
        ];

        try {
            $address = Address::create($address);

            $user = User::find(Auth::id());

            $user->addresses()->attach($address->id);

            if ($request->address["billing_address"]) {
                $this->setShippingOrBilling("billing_address", $address->id);
            }

            if ($request->address["shipping_address"]) {
                $this->setShippingOrBilling("shipping_address", $address->id);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Can't find this type of address",
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully added an address',
            'addresses' => $address,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'nullable',
                'firstName' => 'min:3|string|max:255|required',
                'lastName' => 'min:3|string|max:255|required',
                'street' => 'min:3|string|max:255|required',
                'number' => 'int|required',
                'city' => 'min:3|string|max:255|required',
                'country' => 'min:3|string|max:255|required',
                'zipcode' => 'int|required',
            ]);

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
                    "zipcode" => $request->zipCode,
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

    public function getShippingOrBilling($type)
    {
        try {
            $user = User::find(Auth::id());
            $shippingAddress = $user->addresses()->where($type, true)->first();

            if ($shippingAddress === null) {
                return false;
            }

            return $shippingAddress;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Can't find this type of address",
            ]);
        }
    }

    public function setShippingOrBilling($type, $id)
    {
        try {
            $user = User::find(Auth::id());

            $check = $user->addresses()->where('addresses.id', $id)->exists();
            $previousAddress = $this->getShippingOrBilling($type);

            if ($previousAddress) {
                $previousAddress->pivot->update([$type => false]);
            }

            if ($check) {
                $address = Address::find($id);
                $user->addresses()->where('addresses.id', $id)->updateExistingPivot($address->id, [$type => true]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully changed your ' . str_replace('_', ' ', $type),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No address has been found',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Can't find this type of address",
            ]);
        }
    }
}
