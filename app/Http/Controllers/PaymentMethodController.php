<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Http\Resources\PaymentMethodCollection;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function create(PaymentMethodRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paymentMethod = PaymentMethod::create($data);
        return (new PaymentMethodResource($paymentMethod))->response()->setStatusCode(201);
    }

    public function update(PaymentMethodRequest $request, string $id): PaymentMethodResource
    {
        $data = $request->validated();

        $paymentMethodData = PaymentMethod::find($id);
        if (!$paymentMethodData) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $paymentMethodData->update($data);
        return new PaymentMethodResource($paymentMethodData);
    }

    public function list(): PaymentMethodCollection
    {
        $paymentMethod = PaymentMethod::all();
        return new PaymentMethodCollection($paymentMethod);
    }

    public function get(string $id): PaymentMethodResource
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new PaymentMethodResource($paymentMethod);
    }

    public function destroy(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $paymentMethod->delete();
        
        return response()->json([
            'data' => true
        ]);
    }
}
