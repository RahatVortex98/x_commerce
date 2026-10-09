@extends('layouts.admin', ['title' => ($review->exists ? 'Edit' : 'Add').' customer review'])

@section('content')
    <div class="mb-7">
        <a href="{{ route('admin.reviews.index') }}" class="text-sm font-medium text-[#85464f] underline underline-offset-4">← Back to customer reviews</a>
        <p class="eyebrow mt-5">Customer voices</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $review->exists ? 'Edit' : 'Add' }} a review</h1>
        <p class="mt-2 text-sm text-[#82766d]">Only publish genuine customer feedback. Reviews appear on the home page after you approve and feature them.</p>
    </div>

    <form method="POST" action="{{ $review->exists ? route('admin.reviews.update', $review) : route('admin.reviews.store') }}"
          class="max-w-3xl space-y-5 rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($review->exists) @method('PUT') @endif
        <div>
            <label for="product_id" class="admin-label">Related product <span class="text-[#a95f68]">*</span></label>
            <select id="product_id" name="product_id" required class="form-control">
                <option value="">Choose a product</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id', $review->product_id) == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
            @error('product_id')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="reviewer_name" class="admin-label">Customer name <span class="text-[#a95f68]">*</span></label>
            <input id="reviewer_name" name="reviewer_name" value="{{ old('reviewer_name', $review->reviewer_name) }}" required maxlength="255" class="form-control">
            @error('reviewer_name')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="rating" class="admin-label">Rating <span class="text-[#a95f68]">*</span></label>
            <select id="rating" name="rating" required class="form-control">
                @foreach (range(5, 1) as $rating)
                    <option value="{{ $rating }}" @selected(old('rating', $review->rating ?? 5) == $rating)>{{ $rating }} {{ str_repeat('★', $rating) }}</option>
                @endforeach
            </select>
            @error('rating')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="body" class="admin-label">Customer feedback <span class="text-[#a95f68]">*</span></label>
            <textarea id="body" name="body" rows="5" required maxlength="2000" class="form-control" placeholder="Use the customer's own words.">{{ old('body', $review->body) }}</textarea>
            @error('body')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div class="space-y-3 border-t border-[#eee7e0] pt-5">
            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#eee7e0] p-4">
                <input type="checkbox" name="is_approved" value="1" @checked(old('is_approved', $review->is_approved ?? false)) class="mt-0.5 rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                <span><span class="block text-sm font-semibold">Approve for public display</span><span class="mt-1 block text-xs text-[#82766d]">Unapproved reviews stay private in the admin.</span></span>
            </label>
            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#eee7e0] p-4">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $review->is_featured ?? false)) class="mt-0.5 rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                <span><span class="block text-sm font-semibold">Feature on the home page</span><span class="mt-1 block text-xs text-[#82766d]">The review must also be approved to appear in the customer review section.</span></span>
            </label>
        </div>
        <div class="flex flex-wrap gap-3 border-t border-[#eee7e0] pt-5">
            <button type="submit" class="button-primary">{{ $review->exists ? 'Save changes' : 'Save customer review' }}</button>
            <a href="{{ route('admin.reviews.index') }}" class="button-secondary">Cancel</a>
        </div>
    </form>
@endsection
