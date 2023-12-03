<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountCreateRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Http\Resources\DiscountCollection;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class DiscountController extends Controller
{
    public function list(string $productId): DiscountCollection
    {
        $discounts = Discount::where('product_id', $productId)->get();
        return new DiscountCollection($discounts);
    }

    public function get(string $productId, string $discountId): DiscountResource
    {
        $discount = Discount::where('product_id', $productId)
            ->where('id', $discountId)
            ->first();

        if (!$discount) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }
        return new DiscountResource($discount);
    }

    public function create(DiscountCreateRequest $request, string $productId): JsonResponse
    {
        if (!Product::find($productId)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $datas = $request->validated();

        if (!$this->checkProductHasVariant($datas['discounts'], $productId)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'variant not found in this product'
                    ]
                ]
            ], 404));
        }

        $discounts = array();
        foreach ($datas['discounts'] as $data) {
            $data['product_id'] = $productId;
            $result = Discount::create($data);
            array_push($discounts, $result);
        }

        return (new DiscountCollection($discounts))
            ->response()
            ->setStatusCode(201);
    }

    public function update(DiscountUpdateRequest $request, string $productId): DiscountCollection
    {
        if (!Product::find($productId)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $datas = $request->validated();
        $isDiscount = $this->checkDiscountExist($datas['discounts'], $productId);
        $isVariant = $this->checkProductHasVariant($datas['discounts'], $productId);

        if (!$isDiscount || !$isVariant) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'bad request'
                    ]
                ]
            ], 400));
        }

        $newDiscountDatas = array();

        foreach ($datas['discounts'] as $discount) {
            $updatedData = Discount::where('product_id', $productId)
                ->where('id', $discount['id'])->first();
            unset($discount['id']);
            $updatedData->update($discount);
            array_push($newDiscountDatas, $updatedData);
        }

        return new DiscountCollection($newDiscountDatas);
    }

    public function destroy(string $productId, string $discountId)
    {
        $discount = Discount::where('product_id', $productId)
            ->where('id', $discountId)
            ->first();

        if (!$discount) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }
        $discount->delete();
        return response()->json([
            'data' => true
        ]);
    }

    protected function checkProductHasVariant(array $discounts, $productId): bool
    {
        foreach ($discounts as $discount) {
            if (
                Variant::where('product_id', $productId)
                ->where('id', $discount['variant_id'])->count() === 0
            ) {
                return false;
            }
        }
        return true;
    }

    protected function checkDiscountExist(array $discounts, string $productId): bool
    {
        foreach ($discounts as $discount) {
            if (
                Discount::where('product_id', $productId)
                ->where('id', $discount['id'])->count() === 0
            ) {
                return false;
            }
        }

        return true;
    }
}
