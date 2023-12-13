<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingRequest;
use App\Http\Resources\ShippingResource;
use App\Models\DeliveryService;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class ShippingController extends Controller
{
    public function create(ShippingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $deliveryService = DeliveryService::find($data['delivery_service_id']);

        if (!$deliveryService) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'delivery service not found'
                    ]
                ]
            ])->setStatusCode(400));
        }

        $shipping = Shipping::create($data);
        $shipping['delivery_service'] = $deliveryService;

        return (new ShippingResource($shipping))
            ->response()
            ->setStatusCode(201);
    }

    public function get(string $id)
    {
        $shipping = Shipping::where('id', $id)
            ->with('delivery_service')
            ->first();


        if (!$shipping) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ShippingResource($shipping);
    }

    public function update(Request $request, string $id)
    {
        $shipping = Shipping::where('id', $id)
            ->with('delivery_service')
            ->first();

        $defaultStatus = ['Packing', 'Sending', 'Received'];

        if (!$shipping || !in_array($request->input('status'), $defaultStatus)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        !in_array($request->input('status'), $defaultStatus) ? 'invalid status' : 'not found'
                    ]
                ]
            ])->setStatusCode(
                in_array($request->input('status'), $defaultStatus) ? 400 : 404
            ));
        }

        $shipping['status'] = $request->input('status');
        if ($request->input('receipt_number')) {
            $shipping['receipt_number'] =  $request->input('receipt_number');
        }
        $shipping->save();

        return new ShippingResource($shipping);
    }

    public function destroy(string $id)
    {
        $shipping = Shipping::find($id);

        if (!$shipping) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $shipping->delete();
        return response([
            'data' => true
        ])->setStatusCode(200);
    }
}
