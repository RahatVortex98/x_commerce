<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        return view('admin.resources.index', [
            'resource' => 'brands',
            'title' => 'Brands',
            'items' => Brand::orderBy('sort')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Brand());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);
        $data['is_top'] = $request->boolean('is_top');
        $data['is_handpicked'] = $request->boolean('is_handpicked');
        $brand = Brand::create($data);
        $this->storeImage($request, $brand);

        return redirect()->route('admin.brands.index')->with('ok', 'Brand created.');
    }

    public function edit(Brand $brand): View
    {
        return $this->form($brand);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);
        $data['is_top'] = $request->boolean('is_top');
        $data['is_handpicked'] = $request->boolean('is_handpicked');
        $brand->update($data);
        $this->storeImage($request, $brand);

        return redirect()->route('admin.brands.index')->with('ok', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('ok', 'Brand deleted.');
    }

    private function form(Brand $brand): View
    {
        return view('admin.resources.form', [
            'resource' => 'brands',
            'title' => 'Brand',
            'record' => $brand,
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'offer_label' => ['nullable', 'string', 'max:255'],
            'sort' => ['required', 'integer', 'min:0'],
            'is_top' => ['sometimes', 'boolean'],
            'is_handpicked' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    private function storeImage(Request $request, Brand $brand): void
    {
        if ($request->hasFile('image')) {
            $brand->addMediaFromRequest('image')->toMediaCollection('tile');
        }
    }
}
