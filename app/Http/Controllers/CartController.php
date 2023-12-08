<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartCreateRequest;
use App\Http\Resources\CartCollecion;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function create(CartCreateRequest $request): JsonResponse
    {
        $data =  $request->validated();

        $isProductHasVariant = Variant::where('id', $data['variant_id'])
            ->where('product_id', $data['product_id'])
            ->first()->count() === 0 ? false : true;

        if (!$isProductHasVariant) {
            throw new HttpResponseException(response([
                'erros' => [
                    'message' => [
                        'bad request'
                    ]
                ]
            ])->setStatusCode(400));
        }

        $cartItem = Cart::create($data);

        return (new CartResource($cartItem))->response()->setStatusCode(201);
    }

    public function get(string $id): CartResource
    {
        $userId = Auth::user()->id;

        $cartItem = Cart::where('user_id', $userId)
            ->where('id', $id)
            ->with(['product', 'product.images', 'variant'])
            ->first();

        if (!$cartItem) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new CartResource($cartItem);
    }


    public function list(): CartCollecion
    {
        $userId = Auth::user()->id;

        $cartItems = Cart::where('user_id', $userId)
            ->with(['product', 'product.images', 'variant'])
            ->get();

        return new CartCollecion($cartItems);
    }

    public function updateQuantity(Request $request, string $id): CartResource
    {
        $userId = Auth::user()->id;
        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $cartItem = Cart::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $cartItem['quantity'] = $data['quantity'];
        $cartItem->save();

        return new CartResource($cartItem);
    }

    public function destroy(string $id): JsonResponse
    {
        $userId = Auth::user()->id;
        $cartItem = Cart::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $cartItem->delete();

        return response()->json([
            'data' => true
        ]);
    }
}
