<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.resources.index', [
            'resource' => 'categories',
            'title' => 'Categories',
            'items' => Category::with('parent')->orderByDesc('is_trending')->orderBy('sort')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Category());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_trending'] = $request->boolean('is_trending');
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('ok', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return $this->form($category);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_trending'] = $request->boolean('is_trending');
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('ok', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('ok', 'Category deleted.');
    }

    private function form(Category $category): View
    {
        $excludedIds = $category->exists ? $category->descendantIds() : [];

        return view('admin.resources.form', [
            'resource' => 'categories',
            'title' => 'Category',
            'record' => $category,
            'categories' => Category::whereNotIn('id', $excludedIds)->orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $excludedIds = $category?->descendantIds() ?? [];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
                Rule::notIn($excludedIds),
            ],
            'sort' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'is_trending' => ['sometimes', 'boolean'],
        ]);
    }
}
