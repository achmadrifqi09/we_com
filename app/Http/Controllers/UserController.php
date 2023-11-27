<?php

namespace App\Http\Controllers;


use App\Http\Requests\UserSignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(UserSignupRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        if (User::where('username', $validatedData['username'])->count() > 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Username already taken'
                    ]
                ]
            ])->setStatusCode(400));
        }

        if (User::where('email', $validatedData['email'])->count() > 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Email already taken'
                    ]
                ]
            ])->setStatusCode(400));
        }

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);
        $user['token'] = $user->createToken('testing')->accessToken;

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request)
    {
        $validatedData = $request->validated();

        if (Auth::attempt(['email' => $validatedData['email'],  'password' => $validatedData['password']])) {

            $user = Auth::user();
            $token = $user->createToken('testing')->accessToken;

            return response([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'toke' => $token
                ]
            ])->setStatusCode(200);
        } else {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Email or password wrong'
                    ]
                ]
            ], 401));
        }
    }
}
