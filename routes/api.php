<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeUnit\FunctionUnit;

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
});

Route::group(['middleware' => ['auth:api', 'administrator', 'owner']], function () {
    Route::get('/categories', [CategoryController::class, 'list']);
    Route::get('/categories/{id}', [CategoryController::class, 'get']);
    Route::post('/categories', [CategoryController::class, 'create']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});



Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);
