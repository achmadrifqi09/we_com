<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/images', [ImageController::class, 'index']);
    Route::post('/images', [ImageController::class, 'create']);
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);

    Route::get('/users/addresses', [AddressController::class, 'list']);
    Route::get('/users/addresses/{id}', [AddressController::class, 'get']);
    Route::post('/users/addresses', [AddressController::class, 'create']);
    Route::delete('/users/addresses/{id}', [AddressController::class, 'destroy']);
    Route::put('/users/addresses/{id}', [AddressController::class, 'update']);

    Route::get('/users/logout', [UserController::class, 'logout']);
    Route::get('/users/current', [UserController::class, 'get']);
    Route::put('/users/current', [UserController::class, 'update']);
    Route::post('/users/current/avatar', [UserController::class, 'updateAvatar']);
    Route::put('/users/current/password', [UserController::class, 'updatePassword']);

    Route::get('/products', [ProductController::class, 'list']);
    Route::get('/products/{id}', [ProductController::class, 'get']);

    Route::get('/products/{productId}/variants/{variantId}', [
        VariantController::class, 'get'
    ]);
    Route::get('/products/{productId}/variants', [
        VariantController::class, 'list'
    ]);
});

Route::group(['middleware' => ['auth:api', 'administrator_owner']], function () {
    Route::get('/categories', [CategoryController::class, 'list']);
    Route::get('/categories/{id}', [CategoryController::class, 'get']);
    Route::post('/categories', [CategoryController::class, 'create']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::post('/products', [ProductController::class, 'create']);
    Route::put('/products/{productId}', [ProductController::class, 'update']);
    Route::delete('/products/{productId}', [ProductController::class, 'destroy']);

    Route::post('/products/{productId}/images', [ProductImageController::class, 'create']);
    Route::delete('/products/{productId}/images/{imageId}', [ProductImageController::class, 'destroy']);

    Route::post('/products/{productId}/variants', [
        VariantController::class, 'create'
    ]);
    Route::put('/products/{productId}/variants{variantId}', [
        VariantController::class, 'updateQuantity'
    ]);
    Route::put('/products/{productId}/variants', [
        VariantController::class, 'update'
    ]);
    Route::delete('/products/{productId}/variants/{variantId}', [
        VariantController::class, 'destroy'
    ]);

    Route::post('/products/{productId}/discounts', [DiscountController::class, 'create']);
    Route::put('/products/{productId}/discounts', [DiscountController::class, 'update']);
    Route::get('/products/{productId}/discounts', [DiscountController::class, 'list']);
    Route::get('/products/{productId}/discounts/{discountId}', [DiscountController::class, 'get']);
    Route::delete('/products/{productId}/discounts/{discountId}', [DiscountController::class, 'destroy']);
});

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);
