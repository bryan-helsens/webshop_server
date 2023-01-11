<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Public Routes
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);





Route::post("/place-order", [CheckoutController::class, "createOrder"]);

Route::group(['middleware' => ['role:admin', 'auth:api']], function () {
    Route::get('/user', [AdminController::class, "index"]);

    Route::apiResource('products', ProductController::class);
});


Route::group(['middleware' => ['jwtauth', 'role:costumer']], function () {

    Route::put("/update", [UserController::class, "update"])->name('update');
    Route::get("/me", [AuthController::class, "me"])->name('me');

    Route::get("/addresses", [AddressController::class, "all"]);
    Route::get("/address/{id}", [AddressController::class, "getByID"]);
    Route::put("/edit-address/{id}", [AddressController::class, "update"]);
    Route::post("/add-address", [AddressController::class, "add"]);
    Route::delete("/delete-address/{id}", [AddressController::class, "destroy"]);;
    Route::get('/address/{type}', [AddressController::class, "getShippingOrBilling"]);
    Route::put('/address/{type}/{id}', [AddressController::class, "switchShippingOrBilling"]);

    Route::prefix('/cart')->group(function () {
        Route::post('/add/{product:id}', [CartController::class, 'add']);
        Route::post('/remove/{product:id}', [CartController::class, 'remove']);
        Route::post('/update-cart', [CartController::class, 'updateCart']);
        Route::post('/update-quantity/{product:id}', [CartController::class, 'updateQuantity']);
    });
});


// Protected Routes
Route::middleware('jwtauth')->group(function () {
    Route::get("logout", [AuthController::class, "logout"])->name('logout');


    Route::get("/refresh", [AuthController::class, "refresh"])->name('refresh');
    Route::post('/checkToken', [AuthController::class, "checkToken"])->name('checkToken');
});

Route::middleware(["guestOrVerified"])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/product/{product:id}', [ProductController::class, 'show']);
});
