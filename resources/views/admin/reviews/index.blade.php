@extends('layouts.admin', ['title' => 'Customer reviews'])

@section('content')
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Customer voices</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight">Customer reviews</h1>
            <p class="mt-2 text-sm text-[#82766d]">Add a customer quote and choose whether it appears on the home page.</p>
        </div>
        <a href="{{ route('admin.reviews.create') }}" class="button-primary">＋ Add customer review</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#eae2da] bg-white shadow-sm">
        <div class="divide-y divide-[#eee7e0]">
            @forelse ($reviews as $review)
                <article class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start sm:justify-between sm:p-6">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="font-semibold">{{ $review->reviewer_name ?: 'Customer' }}</h2>
                            <span class="text-sm tracking-widest text-[#a95f68]" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating).str_repeat('☆', 5 - $review->rating) }}</span>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-green-50 text-green-800' => $review->is_approved,
                                'bg-amber-50 text-amber-800' => ! $review->is_approved,
                            ])>{{ $review->is_approved ? 'Approved' : 'Hidden until approved' }}</span>
                            @if ($review->is_featured)
                                <span class="rounded-full bg-[#f8eeec] px-2.5 py-1 text-xs font-semibold text-[#85464f]">Home page</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-[#82766d]">About {{ $review->product?->name ?? 'a deleted product' }}</p>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-[#655a52]">{{ $review->body }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-4 sm:pt-1">
                        <a href="{{ route('admin.reviews.edit', $review) }}" class="text-sm font-semibold text-[#85464f] underline underline-offset-4">Edit</a>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this customer review?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-[#82766d] underline underline-offset-4 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#f8eeec] text-xl text-[#a95f68]" aria-hidden="true">★</span>
                    <h2 class="mt-3 font-semibold">No customer reviews added</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Add an approved review and mark it for the home page to feature it.</p>
                    <a href="{{ route('admin.reviews.create') }}" class="mt-4 inline-flex text-sm font-semibold text-[#85464f] underline underline-offset-4">Add customer review</a>
                </div>
            @endforelse
        </div>
    </div>
    @if ($reviews->hasPages())<div class="mt-5">{{ $reviews->links() }}</div>@endif
@endsection
