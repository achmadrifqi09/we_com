<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageCreateRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $data = Image::where('name', $request->input('name'))->first();

        if (!$data || !Storage::exists($data->image)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return response()->file(Storage::path($data->image));
    }

    public function create(ImageCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $filename = time() . null . str_replace(' ', '', $request->file('image')->getClientOriginalName());
        $validatedData['name'] = $filename;
        $validatedData['image'] = $request->file('image')->storeAs('images', $filename);

        $data = Image::create($validatedData);

        return (new ImageResource($data))->response()->setStatusCode(201);
    }

    public function destroy($id)
    {
        $deletedImage = Image::find($id);

        if (!$deletedImage || !Storage::exists($deletedImage->image)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        Storage::delete($deletedImage->image);
        $deletedImage->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
