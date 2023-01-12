<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helper\Cart;
use App\Models\User;
use App\Models\Customer;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwtauth', ['except' => ['login', 'register']]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'password' => bcrypt($data["password"])
        ]);


        $customer = new Customer();
        $names = explode(" ", $user->name);
        $customer->user_id = $user->id;
        $customer->first_name = $names[0];
        $customer->last_name = $names[1] ?? '';
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully registered a new user.',
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

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
        ])->withCookie(cookie("jwt", $cookie, auth()->factory()->getTTL()));
    }

    /*     public function refresh(Request $request)
    {
        $token = Auth::refresh();

        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'access_token' => $token
        ])->withCookie(cookie("jwt", $token, auth()->factory()->getTTL()));
    } */

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'user' => new UserResource($user),
        ]);
    }

    protected function respondWithToken($token)
    {
        $cookie = $this->getCookie($token);

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'user' => new UserResource(auth()->user()),
            'roles' => auth()->user()->roles->pluck('name'),
        ])->withCookie($cookie);
    }

    private function getCookie($token)
    {
        return cookie(
            "jwt",
            $token,
            auth()->factory()->getTTL()
        );
    }

    public function checkToken()
    {
        return response()->json([
            'status' => 'success'
        ]);
    }
}
