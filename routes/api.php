<?php

use App\Http\Controllers\Api\ProductImageController;
use Illuminate\Support\Facades\Route;

Route::post('/products/{product}/images', ProductImageController::class)
    ->middleware(['auth:sanctum', 'abilities:product-images:write', 'role:admin', 'throttle:20,1'])
    ->name('api.products.images.store');
