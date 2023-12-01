<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageCreateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Role;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        $customerRoleId = Role::where('name', 'Customer')->first()->id;
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['role_id'] = $customerRoleId;
        $user = User::create($validatedData);
        $user['token'] = $user->createToken(env("APP_SECRET_KEY"))->accessToken;

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function get(): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();
        $user = User::find(Auth::user()->id);

        Log::info($user);

        if (User::where('email', '!=', $user['email'])->where('email', $data['email'])->count() === 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Email already taken'
                    ]
                ]
            ])->setStatusCode(400));
        }

        $user->update($data);
        return new UserResource($user);
    }

    public function updateAvatar(ImageCreateRequest $request): UserResource
    {
        $user = User::find(Auth::user()->id);
        $data = $request->validated();
        $avatar = Image::where('id', $user->avatar_id)->first();

        $filename = time() . null . str_replace(' ', '', $request->file('image')->getClientOriginalName());
        $data['name'] = $filename;
        $data['image'] = $request->file('image')->storeAs('images', $filename);

        if ($avatar && Storage::exists($avatar->image)) {
            Storage::delete($avatar->image);
            $avatar->update($data);
        } else {
            $avatar = Image::create($data);
        }

        $user['avatar_id'] = $avatar->id;
        $user->save();

        return new UserResource($user);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::find(Auth::user()->id);

        if (!Hash::check($data['old_password'], $user['password'])) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'The old password is wrong'
                    ]
                ]
            ])->setStatusCode(400));
        }

        $user['password'] = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function login(UserLoginRequest $request)
    {
        $validatedData = $request->validated();

        if (Auth::attempt(['email' => $validatedData['email'],  'password' => $validatedData['password']])) {

            $user = Auth::user();
            $token = $user->createToken(env("APP_SECRET_KEY"))->accessToken;

            return response([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'token' => $token
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

    public function logout(): JsonResponse
    {
        $user = Auth::user()->token();
        $user->revoke();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
