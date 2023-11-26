<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function signup(UserSignupRequest $request)
    {
        $data = $request->validated();

        $duplicateResponse = $this->checkDuplicateData($data['username'], $data['email']);

        if (count($duplicateResponse['errors']) >= 1) {
            throw new HttpResponseException(response()->json($duplicateResponse)->setStatusCode(400));
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    private function checkDuplicateData(string $username, string $email): array
    {
        $response = [
            'errors' => []
        ];

        if (User::where('email', $email)->count() > 1) {
            array_push($response['errors'], 'The email has already been taken');
        } else if (User::where('username', $username)->count() > 1) {
            array_push($response['errors'], 'The username has already been taken');
        }

        return $response;
    }
}
