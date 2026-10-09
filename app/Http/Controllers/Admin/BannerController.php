<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.resources.index', [
            'resource' => 'banners',
            'title' => 'Banners',
            'items' => Banner::orderBy('sort')->orderBy('title')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Banner());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['desktop_image'], $data['mobile_image']);
        $data['is_active'] = $request->boolean('is_active');
        $banner = Banner::create($data);
        $this->storeImages($request, $banner);

        return redirect()->route('admin.banners.index')->with('ok', 'Banner created.');
    }

    public function edit(Banner $banner): View
    {
        return $this->form($banner);
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['desktop_image'], $data['mobile_image']);
        $data['is_active'] = $request->boolean('is_active');
        $banner->update($data);
        $this->storeImages($request, $banner);

        return redirect()->route('admin.banners.index')->with('ok', 'Banner updated.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('ok', 'Banner deleted.');
    }

    private function form(Banner $banner): View
    {
        return view('admin.resources.form', [
            'resource' => 'banners',
            'title' => 'Banner',
            'record' => $banner,
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:255'],
            'sort' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'desktop_image' => ['nullable', 'image', 'max:5120'],
            'mobile_image' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    private function storeImages(Request $request, Banner $banner): void
    {
        foreach (['desktop_image' => 'desktop', 'mobile_image' => 'mobile'] as $input => $collection) {
            if ($request->hasFile($input)) {
                $banner->addMediaFromRequest($input)->toMediaCollection($collection);
            }
        }
    }
}
