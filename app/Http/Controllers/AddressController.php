<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public function getAddress(string $addressId, string $userId): Address
    {
        $address = Address::where('user_id', $userId)
            ->where('id', $addressId)->first();

        if (!$address) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function get(string $id): AddressResource
    {
        $user = Auth::user();
        $address = $this->getAddress($id, $user->id);

        return new AddressResource($address);
    }

    public function create(AddressRequest $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validated();
        $data['user_id'] = $user->id;
        $address = Address::create($data);

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function list(): AddressCollection
    {
        $user = Auth::user();

        $addresses = Address::where('user_id', $user->id)->get();

        return new AddressCollection($addresses);
    }

    public function update(AddressRequest $request, string $id): AddressResource
    {
        $user = Auth::user();
        $address = $this->getAddress($id, $user->id);
        $data = $request->validated();

        $address->update($data);

        return new AddressResource($address);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();
        $address = $this->getAddress($id, $user->id);
        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
