@extends('layouts.admin', ['title' => $title])

@section('content')
    @php
        $singular = ['products' => 'product', 'categories' => 'category', 'brands' => 'brand', 'banners' => 'banner', 'concerns' => 'concern'][$resource];
        $hint = [
            'products' => 'Add and update the items customers can buy.',
            'categories' => 'Organize products into easy-to-browse sections.',
            'brands' => 'Manage the makers featured in your store.',
            'banners' => 'Add promotions to the top of your home page.',
            'concerns' => 'Match products to needs such as acne, dark circles or sun protection.',
        ][$resource];
    @endphp
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Store content</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $title }}</h1>
            <p class="mt-2 text-sm text-[#82766d]">{{ $hint }}</p>
        </div>
        <a href="{{ route("admin.$resource.create") }}" class="button-primary">＋ Add {{ $singular }}</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#eae2da] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#eee7e0] px-5 py-4">
            <p class="text-sm font-medium">{{ $items->total() }} {{ strtolower($title) }}</p>
            <p class="text-xs text-[#82766d]">Choose “Edit” to change an item</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#eee7e0] text-left text-sm">
                <thead class="bg-[#faf7f2] text-xs uppercase tracking-wide text-[#82766d]">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-semibold">Name</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Details</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0eae5]">
                    @forelse ($items as $item)
                        @php($name = $item->title ?? $item->name)
                        <tr class="transition hover:bg-[#fffdfb]">
                            <th scope="row" class="whitespace-nowrap px-5 py-4 font-semibold">{{ $name }}</th>
                            <td class="px-5 py-4 text-[#655a52]">
                                @if ($resource === 'products')
                                    <div>{{ $item->brand?->name ?? 'No brand' }} <span class="mx-1 text-[#c2b6ae]">·</span> {{ config('shop.currency') }}{{ number_format($item->price, 2) }}</div>
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @if ($item->is_new)<span class="rounded-full bg-[#f8eeec] px-2 py-0.5 text-[10px] font-semibold text-[#85464f]">New arrival</span>@endif
                                        @if ($item->is_combo)<span class="rounded-full bg-[#f4f0e8] px-2 py-0.5 text-[10px] font-semibold text-[#655a52]">Combo</span>@endif
                                        @if ($item->is_recommended)<span class="rounded-full bg-[#f8eeec] px-2 py-0.5 text-[10px] font-semibold text-[#85464f]">Recommended</span>@endif
                                        @if ($item->compare_price > $item->price)<span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-800">Discount</span>@endif
                                    </div>
                                @elseif ($resource === 'categories')
                                    <div>{{ $item->parent?->name ?? 'Main category' }}</div>
                                    @if ($item->is_trending)<span class="mt-1 inline-flex rounded-full bg-[#f8eeec] px-2 py-0.5 text-[10px] font-semibold text-[#85464f]">Trending</span>@endif
                                @elseif ($resource === 'brands')
                                    {{ $item->offer_label ?: 'No offer added' }}
                                @elseif ($resource === 'concerns')
                                    {{ ucfirst($item->type) }} <span class="mx-1 text-[#c2b6ae]">·</span> {{ $item->products_count }} matched products
                                @else
                                    {{ $item->link ?: 'No destination link' }}
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($resource === 'products' || $resource === 'categories' || $resource === 'banners')
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-green-50 text-green-800' => $item->is_active,
                                        'bg-gray-100 text-gray-600' => ! $item->is_active,
                                    ])>{{ $item->is_active ? 'Visible' : 'Hidden' }}</span>
                                @elseif ($resource === 'brands')
                                    <span class="text-xs text-[#655a52]">
                                        {{ $item->is_top ? 'Featured' : ($item->is_handpicked ? 'Editor’s pick' : 'Standard') }}
                                    </span>
                                @else
                                    <span class="text-xs text-[#655a52]">{{ $item->products_count }} matched products</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <a href="{{ route("admin.$resource.edit", $item) }}" class="font-semibold text-[#85464f] underline underline-offset-4">Edit</a>
                                <form method="POST" action="{{ route("admin.$resource.destroy", $item) }}" class="ml-4 inline"
                                      onsubmit="return confirm('Delete this {{ $singular }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[#82766d] underline underline-offset-4 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-14 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#f8eeec] text-xl text-[#a95f68]" aria-hidden="true">＋</span>
                                <p class="mt-3 font-semibold">No {{ strtolower($title) }} yet</p>
                                <p class="mt-1 text-sm text-[#82766d]">Add your first {{ $singular }} to get started.</p>
                                <a href="{{ route("admin.$resource.create") }}" class="mt-4 inline-flex text-sm font-semibold text-[#85464f] underline underline-offset-4">Add {{ $singular }}</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($items->hasPages())
        <div class="mt-5">{{ $items->links() }}</div>
    @endif
@endsection
