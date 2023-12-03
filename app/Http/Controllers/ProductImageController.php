<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImageCreateRequest;
use App\Http\Resources\ProductImageCollection;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\ProductImage;


class ProductImageController extends Controller
{
    public function create(ProductImageCreateRequest $request, $productId): ProductImageCollection
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
        $images = array();

        foreach ($datas['images'] as $image) {
            $filename = time() . null . str_replace(' ', '', $image->getClientOriginalName());
            $path = $image->storeAs('product-images', $filename);

            $result = ProductImage::create([
                'name' => $filename,
                'product_id' => $productId,
                'path' => $path
            ]);
            array_push($images, $result);
        }

        return new ProductImageCollection($images);
    }

    public function destroy(string $productId, string $imageId)
    {
        $image = ProductImage::where('product_id', $productId)
            ->where('id', $imageId)
            ->first();

        if (!$image) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        if (Storage::exists($image['path'])) {
            Storage::delete($image['path']);
        }
        $image->delete();

        return response()->json([
            'data' => true
        ]);
    }
}
