<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        //$this->middleware('jwtauth');
    }

    public function all()
    {
        return User::all();
    }



    public function update(Request $request)
    {
        $request->validate([
            'firstName' => 'min:3|string|max:255',
            'lastName' => 'min:3|string|max:255',
            'phone' => 'string|max:255',
        ]);

        $user = Auth::user();

        $user->update([
            "firstName" => $request->firstName,
            "lastName" => $request->lastName,
            "phone" => $request->phone,
        ]);

        return Auth::user();
    }
}
