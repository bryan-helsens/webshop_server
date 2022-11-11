<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwtauth', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        User::create([
            'name' => $fields["name"],
            'email' => $fields["email"],
            'password' => bcrypt($fields["password"])
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully registered a new user.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'message' => 'Invalid credentials!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        /*  $cookie = cookie('jwt', $token, 60 * 24); // 1 day */
        return $this->respondWithToken($token);
    }

    public function logout()
    {

        Auth::logout();
        $cookie = Cookie::forget('jwt');

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ])->withCookie(cookie("jwt", $cookie, auth()->factory()->getTTL() * 60));
    }

    public function refresh(Request $request)
    {

        $token = Auth::refresh();

        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'access_token' => $token
        ])->withCookie(cookie("jwt", $token, auth()->factory()->getTTL() * 60));
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'user' => auth()->user(),
            'roles' => auth()->user()->roles->pluck('name'),
        ])->withCookie(cookie("jwt", $token, auth()->factory()->getTTL() * 60));
    }

    public function checkToken()
    {
        return response()->json([
            'status' => 'success'
        ]);
    }
}