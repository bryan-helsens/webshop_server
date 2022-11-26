<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'user.email' => 'string|required|email',
            'user.firstName' => 'min:3|string|max:255|required',
            'user.lastName' => 'min:3|string|max:255|required',
            'user.name' => 'min:3|string|max:255|required',
            'user.phone' => 'string|required',
        ]);

        $user = User::find(Auth::id());
        $user->update($request->user);

        return response()->json([
            'status' => 'success',
            'message' =>  'Account Updated',
        ]);
    }
}
