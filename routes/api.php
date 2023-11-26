<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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


Route::get('/images', [ImageController::class, 'index']);
Route::post('/images', [ImageController::class, 'create']);
Route::delete('/images/{id}', [ImageController::class, 'destroy']);

Route::post('/users', [UserController::class, 'signup']);
