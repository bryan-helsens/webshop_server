<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserController;

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

//Route::Resource('products', ProductController::class);

// Public Routes
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);




Route::get("/products", [ProductController::class, "index"]);
Route::get("/products/{id}", [ProductController::class, "show"]);
Route::get("/products/list/{category}", [ProductController::class, "list"]);

Route::post("/place-order", [CheckoutController::class, "createOrder"]);


Route::group(['middleware' => ['role:admin', 'auth:api']], function () {
    Route::get('/user', [AdminController::class, "index"]);
});


Route::group(['middleware' => ['role:costumer', 'auth:api']], function () {
    Route::get("/me", [AuthController::class, "me"])->name('me');
    Route::put("/update", [UserController::class, "update"])->name('update');


    Route::get("/addresses", [AddressController::class, "all"]);
    Route::get("/address/{id}", [AddressController::class, "getByID"]);
    Route::put("/edit-address/{id}", [AddressController::class, "update"]);
    Route::post("/add-address", [AddressController::class, "add"]);
    Route::delete("/delete-address/{id}", [AddressController::class, "destroy"]);;
    Route::get('/address/{type}', [AddressController::class, "getShippingOrBilling"]);
    Route::put('/address/{type}/{id}', [AddressController::class, "setShippingOrBilling"]);
});



// Protected Routes
Route::middleware('jwtauth')->group(function () {
    Route::get("logout", [AuthController::class, "logout"])->name('logout');

    Route::get("/refresh", [AuthController::class, "refresh"])->name('refresh');
    Route::post('/checkToken', [AuthController::class, "checkToken"])->name('checkToken');

    Route::post("/products", [ProductController::class, "store"]);
    Route::put("/products/{id}", [ProductController::class, "update"]);
    Route::delete("/products/{id}", [ProductController::class, "destroy"]);
});
