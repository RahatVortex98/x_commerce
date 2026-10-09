<?php
namespace App\Http\Controllers;

use App\Models\{Banner, Brand, Category, Concern, Ingredient, Product, Review};

class HomeController extends Controller
{
    public function __invoke()
    {
        $with = ['brand', 'media', 'variants'];
        return view('home', [
            'banners'      => Banner::where('is_active', true)->orderBy('sort')->with('media')->get(),
            'trendingCategories' => Category::where('is_active', true)->where('is_trending', true)->orderBy('sort')->take(6)->get(),
            'recommended' => Product::active()->where('is_recommended', true)->latest()->with($with)->take(6)->get(),
            'newArrivals'  => Product::active()->where('is_new', true)->latest()->with($with)->take(6)->get(),
            'discounts' => Product::active()->whereNotNull('compare_price')->whereColumn('compare_price', '>', 'price')->latest()->with($with)->take(6)->get(),
            'topBrands'    => Brand::where('is_top', true)->orderBy('sort')->with('media')->take(6)->get(),
            'handpicked'   => Brand::where('is_handpicked', true)->orderBy('sort')->with('media')->take(6)->get(),
            'combos'       => Product::active()->where('is_combo', true)->latest()->with($with)->take(12)->get(),
            'concerns'     => Concern::with(['products' => fn ($query) => $query->active()->with($with)])
                ->orderBy('sort')->orderBy('name')->get()->groupBy('type'),
            'reviews' => Review::where('is_approved', true)->where('is_featured', true)
                ->with(['product.media'])->latest()->take(6)->get(),
            'ingredients'  => Ingredient::orderBy('sort')->get(),
        ]);
    }
}