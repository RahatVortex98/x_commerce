@extends('layouts.shop', ['title' => $product->name.' | '.config('app.name')])

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
        <nav class="mb-7 text-sm text-[#82766d]" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#85464f]">Home</a>
            <span class="mx-2">/</span>
            @if ($product->brand)
                <a href="{{ route('brand', $product->brand) }}" class="hover:text-[#85464f]">{{ $product->brand->name }}</a>
                <span class="mx-2">/</span>
            @endif
            <span class="text-[#493735]">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-9 lg:grid-cols-2 lg:gap-16">
            <div class="space-y-3">
                @if ($product->getMedia('images')->isNotEmpty())
                    <div x-data="{ activeImage: 0 }">
                        <div class="overflow-hidden rounded-[2rem] border border-[#eae2da] bg-white">
                            @foreach ($product->getMedia('images') as $index => $image)
                                <img x-show="activeImage === {{ $index }}" x-cloak
                                     src="{{ $image->getUrl('large') }}" alt="{{ $product->name }} photo {{ $index + 1 }}"
                                     class="aspect-square w-full object-cover">
                            @endforeach
                        </div>
                        @if ($product->getMedia('images')->count() > 1)
                            <div class="mt-3 flex gap-3 overflow-x-auto" role="group" aria-label="Product photos">
                                @foreach ($product->getMedia('images') as $index => $image)
                                    <button type="button" @click="activeImage = {{ $index }}"
                                            :aria-pressed="(activeImage === {{ $index }}).toString()"
                                            :class="activeImage === {{ $index }} ? 'border-[#85464f]' : 'border-transparent'"
                                            class="shrink-0 overflow-hidden rounded-xl border-2 border-transparent focus:border-[#85464f]">
                                        <img src="{{ $image->getUrl('thumb') }}" alt="Show product photo {{ $index + 1 }}"
                                             class="h-16 w-16 object-cover sm:h-20 sm:w-20">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex aspect-square items-center justify-center overflow-hidden rounded-[2rem] border border-[#eae2da] bg-white p-8 text-center">
                        <div><span class="text-5xl text-[#c58f83]" aria-hidden="true">✿</span><p class="mt-3 text-sm text-[#82766d]">Product photo coming soon</p></div>
                    </div>
                @endif
            </div>

            <div class="py-2 lg:py-7">
                @if ($product->brand)
                    <a href="{{ route('brand', $product->brand) }}" class="eyebrow hover:underline">{{ $product->brand->name }}</a>
                @else
                    <p class="eyebrow">Beauty, chosen with care</p>
                @endif
                <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $product->name }}</h1>
                <div class="mt-5 flex items-baseline gap-3">
                    <span class="text-2xl font-semibold">{{ config('shop.currency') }}{{ number_format($product->price, 2) }}</span>
                    @if ($product->compare_price > $product->price)
                        <span class="text-sm text-[#82766d] line-through">{{ config('shop.currency') }}{{ number_format($product->compare_price, 2) }}</span>
                        <span class="rounded-full bg-[#f8eeec] px-3 py-1 text-xs font-semibold text-[#85464f]">Save {{ $product->discount_percent }}%</span>
                    @endif
                </div>

                @if ($product->description)
                    <div class="mt-6 whitespace-pre-line text-sm leading-7 text-[#655a52]">{{ $product->description }}</div>
                @endif

                @if ($product->in_stock)
                    <form method="POST" action="{{ route('cart.add') }}" class="mt-8 space-y-5">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @if ($product->variants->isNotEmpty())
                            <div>
                                <label for="variant_id" class="admin-label">Choose a size or shade</label>
                                <select id="variant_id" name="variant_id" required class="form-control">
                                    @foreach ($product->variants->where('stock', '>', 0) as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->name }}@if ($variant->price ?? false) — {{ config('shop.currency') }}{{ number_format($variant->price, 2) }}@endif</option>
                                    @endforeach
                                </select>
                                @error('variant_id')<p class="admin-error">{{ $message }}</p>@enderror
                            </div>
                        @endif
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="w-24">
                                <label for="qty" class="admin-label">Quantity</label>
                                <input id="qty" name="qty" type="number" min="1" max="10" value="1" class="form-control">
                            </div>
                            <button type="submit" class="button-primary min-w-48">Add to bag</button>
                        </div>
                    </form>
                @else
                    <p class="mt-8 rounded-xl bg-[#f8eeec] px-4 py-3 text-sm font-medium text-[#85464f]">Currently out of stock. Please check back soon.</p>
                @endif

                <div class="mt-8 border-t border-[#eae2da] pt-5">
                    <p class="text-sm font-semibold">A little reassurance</p>
                    <ul class="mt-3 space-y-2 text-sm text-[#82766d]">
                        <li>Authentic products, chosen with care</li>
                        <li>Cash on delivery available</li>
                        <li>Delivery across Bangladesh</li>
                    </ul>
                </div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-16">
                <div class="mb-6"><p class="eyebrow">More from {{ $product->brand?->name ?? 'our collection' }}</p><h2 class="section-title mt-2">You might also like</h2></div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($related as $relatedProduct) <x-product-card :product="$relatedProduct" /> @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
