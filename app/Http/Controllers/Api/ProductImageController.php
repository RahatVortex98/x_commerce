<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __invoke(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $media = $product
            ->addMedia($validated['image'])
            ->toMediaCollection('images');

        return response()->json([
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'slug' => $product->slug,
                ],
                'image' => [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'thumbnail_url' => $media->getUrl('thumb'),
                    'large_url' => $media->getUrl('large'),
                ],
            ],
        ], 201);
    }
}
