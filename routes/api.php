<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

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


Route::group(['middleware' => ['role:admin', 'auth:api']], function () {
    Route::get('/user', [AdminController::class, "index"]);
});



// Protected Routes
Route::middleware('auth:api')->group(function () {
    Route::get("logout", [AuthController::class, "logout"])->name('logout');

    Route::get("/me", [AuthController::class, "me"])->name('me');
    Route::get("/refresh", [AuthController::class, "refresh"])->name('refresh');
    Route::post('/checkToken', [AuthController::class, "checkToken"])->name('checkToken');

    Route::post("/products", [ProductController::class, "store"]);
    Route::put("/products/{id}", [ProductController::class, "update"]);
    Route::delete("/products/{id}", [ProductController::class, "destroy"]);
});
