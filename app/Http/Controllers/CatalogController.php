<?php
namespace App\Http\Controllers;

use App\Models\{Brand, Category, Product};
use Spatie\QueryBuilder\{AllowedFilter, QueryBuilder};

class CatalogController extends Controller
{
    private function listing($base)
    {
        return QueryBuilder::for($base->active()->with(['brand', 'media', 'variants']))
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::exact('brand_id'),
                AllowedFilter::scope('in_stock'),
                AllowedFilter::scope('price_between'),
                AllowedFilter::scope('concern'),
                AllowedFilter::scope('ingredient'),
            )
            ->allowedSorts('price', 'created_at', 'name')
            ->defaultSort('-created_at')
            ->paginate(24)->withQueryString();
    }

    public function category(Category $category)
    {
        return view('catalog.index', [
            'title'    => $category->name,
            'children' => $category->children,
            'products' => $this->listing(Product::category($category->slug)),
        ]);
    }

    public function brand(Brand $brand)
    {
        return view('catalog.index', [
            'title' => $brand->name, 'children' => collect(),
            'products' => $this->listing(Product::where('brand_id', $brand->id)),
        ]);
    }

    public function search()
    {
        return view('catalog.index', [
            'title' => 'Search', 'children' => collect(),
            'products' => $this->listing(Product::query()),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'media', 'variants', 'reviews', 'ingredients']);
        $related = Product::active()->where('brand_id', $product->brand_id)
            ->whereKeyNot($product->id)->with(['brand', 'media', 'variants'])->take(6)->get();
        return view('catalog.show', compact('product', 'related'));
    }
}