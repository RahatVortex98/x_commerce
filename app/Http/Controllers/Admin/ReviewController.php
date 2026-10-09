<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Review};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.reviews.index', [
            'reviews' => Review::with('product')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Review());
    }

    public function store(Request $request): RedirectResponse
    {
        Review::create($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('ok', 'Customer review added.');
    }

    public function edit(Review $review): View
    {
        return $this->form($review);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $review->update($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('ok', 'Customer review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('ok', 'Customer review deleted.');
    }

    private function form(Review $review): View
    {
        return view('admin.reviews.form', [
            'review' => $review,
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'reviewer_name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['required', 'string', 'max:2000'],
            'is_approved' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ]);
        $data['user_id'] = null;
        $data['is_approved'] = $request->boolean('is_approved');
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }
}
