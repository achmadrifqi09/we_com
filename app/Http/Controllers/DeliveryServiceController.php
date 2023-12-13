<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryServiceRequest;
use App\Http\Resources\DeliveryServiceCollection;
use App\Http\Resources\DeliveryServiceResource;
use App\Models\DeliveryService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class DeliveryServiceController extends Controller
{
    public function create(DeliveryServiceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $deliveryService = DeliveryService::create($data);

        return (new DeliveryServiceResource($deliveryService))
            ->response()
            ->setStatusCode(201);
    }

    public function update(DeliveryServiceRequest $request, string $id): DeliveryServiceResource
    {
        $data = $request->validated();

        $deliveryService = DeliveryService::find($id);

        if (!$deliveryService) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $deliveryService->update($data);
        return new DeliveryServiceResource($deliveryService);
    }

    public function get(string $id)
    {
        $deliveryService = DeliveryService::find($id);
        if (!$deliveryService) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new DeliveryServiceResource($deliveryService);
    }

    public function list()
    {
        $deliveryService = DeliveryService::all();
        return new DeliveryServiceCollection($deliveryService);
    }

    public function destroy(string $id)
    {
        $deliveryService = DeliveryService::find($id);
        if (!$deliveryService) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $deliveryService->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
