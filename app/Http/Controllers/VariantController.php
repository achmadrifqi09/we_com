<?php

namespace App\Http\Controllers;

use App\Http\Requests\VariantCreateRequest;
use App\Http\Requests\VariantUpdateRequest;
use App\Http\Resources\VariantCollection;
use App\Http\Resources\VariantResource;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VariantController extends Controller
{
    public function create(VariantCreateRequest $request, $productId): JsonResponse
    {
        if (!Product::find($productId)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'product not found'
                    ]
                ]
            ], 404));
        }

        $datas = $request->validated();

        $variants = array();

        foreach ($datas['variants'] as $variant) {
            $variant['product_id'] = $productId;
            $result = Variant::create($variant);
            array_push($variants, $result);
        }
        $this->updateTotalStock($productId);
        return (new VariantCollection($variants))
            ->response()
            ->setStatusCode(201);
    }

    public function updateQuantity(Request $request, string $productId, string $variantId): VariantResource
    {
        $quantity = $request->input('quantity');
        $productVariant = Variant::where('product_id', $productId)
            ->where('id', $variantId)
            ->first();

        if (!$productVariant) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $productVariant['quantity'] = $quantity;
        $productVariant->save();
        $this->updateTotalStock($productId);

        return new VariantResource($productVariant);
    }

    public function update(VariantUpdateRequest $request, $productId): VariantCollection
    {
        if (!Product::find($productId)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'product not found'
                    ]
                ]
            ], 404));
        }

        $datas = $request->validated();

        $newData = array();

        foreach ($datas['variants'] as $data) {
            $variant = Variant::where('product_id', $productId)
                ->where('id', $data['id'])->first();

            if (!$variant) {
                throw new HttpResponseException(response([
                    'errors' => [
                        'message' => [
                            'id: ' . $data['id'] . ' not found'
                        ]
                    ]
                ], 404));
            }

            unset($data['id']);
            $variant->update($data);
            array_push($newData, $variant);
        }

        $this->updateTotalStock($productId);
        return new VariantCollection($newData);
    }

    public function list(string $productId): VariantCollection
    {
        if (!Product::find($productId)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'product not found'
                    ]
                ]
            ], 404));
        }
        $variants = Variant::where('product_id', $productId)->get();
        return new VariantCollection($variants);
    }

    public function get(string $productId, $variantId): VariantResource
    {
        $variant = Variant::where('product_id', $productId)
            ->where('id', $variantId)
            ->first();

        if (!$variant) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }
        Log::info($variant);
        return new VariantResource($variant);
    }

    public function destroy(string $productId, $variantId)
    {
        $variant = Variant::where('product_id', $productId)
            ->where('id', $variantId)
            ->first();

        if (!$variant) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }
        $variant->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    protected function updateTotalStock(string $productId)
    {
        $product = Product::find($productId);
        $variants = Variant::where('product_id', $productId)->get();

        $product['stock_total'] = $this->getTotalStock($variants);
        $product->save();
    }

    protected function getTotalStock($variants): int
    {
        $stock = 0;
        foreach ($variants as $variant) {
            $stock += $variant['quantity'];
        }
        return $stock;
    }
}
