<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Brand, Category, Concern, Product};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.resources.index', [
            'resource' => 'products',
            'title' => 'Products',
            'items' => Product::with('brand')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Product());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $categoryIds = $data['categories'] ?? [];
        $concernIds = $data['concerns'] ?? [];
        unset($data['categories'], $data['concerns'], $data['image']);
        $data['is_combo'] = $request->boolean('is_combo');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_active'] = $request->boolean('is_active');
        $data['is_recommended'] = $request->boolean('is_recommended');

        $product = Product::create($data);
        $product->categories()->sync($categoryIds);
        $product->concerns()->sync($concernIds);
        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('admin.products.index')->with('ok', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return $this->form($product);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);
        $categoryIds = $data['categories'] ?? [];
        $concernIds = $data['concerns'] ?? [];
        unset($data['categories'], $data['concerns'], $data['image']);
        $data['is_combo'] = $request->boolean('is_combo');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_active'] = $request->boolean('is_active');
        $data['is_recommended'] = $request->boolean('is_recommended');

        $product->update($data);
        $product->categories()->sync($categoryIds);
        $product->concerns()->sync($concernIds);
        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('admin.products.index')->with('ok', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('ok', 'Product deleted.');
    }

    private function form(Product $product): View
    {
        return view('admin.resources.form', [
            'resource' => 'products',
            'title' => 'Product',
            'record' => $product,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'concerns' => Concern::orderBy('type')->orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')],
            'concerns' => ['sometimes', 'array'],
            'concerns.*' => ['integer', Rule::exists('concerns', 'id')],
            'is_combo' => ['sometimes', 'boolean'],
            'is_new' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'is_recommended' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);
    }
}
