<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Banner, Brand, Category, Concern, Product, Review};
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'products' => Product::count(),
                'categories' => Category::count(),
                'brands' => Brand::count(),
                'banners' => Banner::count(),
                'concerns' => Concern::count(),
                'reviews' => Review::count(),
            ],
        ]);
    }
}
