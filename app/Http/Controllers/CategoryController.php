<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class CategoryController extends Controller
{
    public function create(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = Category::create($data);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function list(): CategoryCollection
    {
        $categories = Category::all();
        return new CategoryCollection($categories);
    }

    public function get(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function update(CategoryRequest $request, string $id): JsonResponse
    {
        $category = Category::find($id);
        $data = $request->validated();

        if (!$category) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $category->update($data);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 404));
        }

        $category->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
