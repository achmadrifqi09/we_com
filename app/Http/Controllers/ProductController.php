<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductCreateResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function create(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['stock_total'] = 0;
        $data['rating_average'] = 0;
        $data['amount_sold'] = 0;

        $product = Product::create($data);
        return (new ProductCreateResource($product))->response()->setStatusCode(201);
    }

    public function update(ProductRequest $request, string $productId)
    {
        $data = $request->validated();
        $product = Product::find($productId);

        if (!$product) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $product['name'] = $data['name'];
        $product['description'] = $data['description'];
        $product['category_id'] = $data['category_id'];

        $product->save();
        $product->with('variants', 'images');
        return new ProductResource($product);
    }

    public function destroy(string $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $product->delete();
        Variant::where('product_id', $productId)->delete();

        return response()->json([
            'data' => true
        ]);
    }

    public function list(Request $request): ProductCollection
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $products = Product::orderBy('created_at', 'DESC')
            ->with(['variants', 'discount']);

        $products = $products->paginate(perPage: $size, page: $page);
        return new ProductCollection($products);
    }

    public function get(string $id): ProductResource
    {
        $product = Product::find($id);

        if (!$product) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }
        $product;

        return new ProductResource($product);
    }
}
