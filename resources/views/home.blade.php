@extends('layouts.shop', ['title' => config('app.name').' | Beauty, chosen with care'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 sm:pt-8">
        @if ($banners->isNotEmpty())
            <div x-data="{ active: 0 }" class="relative overflow-hidden rounded-[2rem] bg-[#efe2dc]">
                @foreach ($banners as $index => $banner)
                    @php($desktopImage = $banner->getFirstMediaUrl('desktop'))
                    @php($mobileImage = $banner->getFirstMediaUrl('mobile'))
                    <a href="{{ $banner->link ?: route('search') }}"
                       x-show="active === {{ $index }}" x-transition.opacity
                       class="block min-h-64 sm:min-h-96">
                        @if ($desktopImage)
                            <picture>
                                @if ($mobileImage)
                                    <source media="(max-width: 767px)" srcset="{{ $mobileImage }}">
                                @endif
                                <img src="{{ $desktopImage }}" alt="{{ $banner->title }}" class="h-64 w-full object-cover sm:h-96">
                            </picture>
                        @else
                            <div class="flex min-h-64 items-end bg-gradient-to-br from-[#ead2cc] via-[#f4eae2] to-[#c69d91] p-8 sm:min-h-96 sm:p-16">
                                <div class="max-w-xl">
                                    <p class="eyebrow">A little something for you</p>
                                    <h1 class="mt-3 text-4xl font-semibold tracking-tight text-[#493735] sm:text-6xl">{{ $banner->title }}</h1>
                                    <span class="button-primary mt-6">Explore the collection</span>
                                </div>
                            </div>
                        @endif
                    </a>
                @endforeach
                @if ($banners->count() > 1)
                    <div class="absolute bottom-4 right-5 flex gap-2" aria-label="Choose banner">
                        @foreach ($banners as $index => $banner)
                            <button type="button" @click="active = {{ $index }}"
                                    :aria-pressed="(active === {{ $index }}).toString()"
                                    :class="active === {{ $index }} ? 'w-7 bg-[#85464f]' : 'w-2.5 bg-white/80'"
                                    class="h-2.5 rounded-full transition-all" aria-label="Show banner {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="relative overflow-hidden rounded-[2rem] bg-[#f2e4df]">
                <div class="absolute -right-20 -top-24 h-80 w-80 rounded-full bg-[#d5b4a9]/50 blur-2xl sm:right-12 sm:top-0"></div>
                <div class="relative grid min-h-[25rem] items-center gap-8 px-7 py-12 sm:min-h-[34rem] sm:px-14 lg:grid-cols-[1.1fr_.9fr] lg:px-20">
                    <div class="max-w-xl">
                        <p class="eyebrow">Your beauty, your way</p>
                        <h1 class="mt-4 text-4xl font-semibold leading-[1.08] tracking-tight text-[#493735] sm:text-6xl">
                            Feel good in your own skin.
                        </h1>
                        <p class="mt-5 max-w-md text-base leading-7 text-[#75645d] sm:text-lg">
                            Thoughtful skincare, makeup and everyday essentials—picked to make your routine feel like yours.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            @if ($menu->isNotEmpty())
                                <a href="{{ route('category', $menu->first()) }}" class="button-primary">Explore the collection</a>
                            @else
                                <a href="{{ route('search') }}" class="button-primary">Explore the collection</a>
                            @endif
                            <a href="{{ route('search') }}" class="button-secondary">Find your essentials</a>
                        </div>
                    </div>
                    <div class="hidden justify-center lg:flex" aria-hidden="true">
                        <div class="relative flex h-72 w-72 items-center justify-center rounded-full border border-white/70 bg-[#ead8d0]">
                            <div class="flex h-52 w-52 items-center justify-center rounded-full bg-[#c58f83] text-center text-sm font-medium uppercase tracking-[0.2em] text-white shadow-xl">
                                Little rituals<br>big feelings
                            </div>
                            <span class="absolute right-0 top-8 rounded-full bg-white px-4 py-2 text-xs uppercase tracking-widest text-[#85464f]">Made for you</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <section class="border-y border-[#eae2da] bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-4 px-4 py-5 sm:grid-cols-4 sm:px-6">
            <div class="flex items-center gap-3"><span class="text-xl text-[#a95f68]">✳</span><div><p class="text-sm font-semibold">Authentic beauty</p><p class="mt-0.5 text-xs text-[#82766d]">Products chosen with care</p></div></div>
            <div class="flex items-center gap-3"><span class="text-xl text-[#a95f68]">♡</span><div><p class="text-sm font-semibold">Easy delivery</p><p class="mt-0.5 text-xs text-[#82766d]">Across Bangladesh</p></div></div>
            <div class="flex items-center gap-3"><span class="text-xl text-[#a95f68]">৳</span><div><p class="text-sm font-semibold">Pay your way</p><p class="mt-0.5 text-xs text-[#82766d]">Cash or digital payment</p></div></div>
            <div class="flex items-center gap-3"><span class="text-xl text-[#a95f68]">↗</span><div><p class="text-sm font-semibold">Need a hand?</p><p class="mt-0.5 text-xs text-[#82766d]">We're here to help</p></div></div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl space-y-16 px-4 py-14 sm:px-6 sm:py-20">
        <section>
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div><p class="eyebrow">Start with what you love</p><h2 class="section-title mt-2">Explore our collections</h2></div>
                <a href="{{ route('search') }}" class="text-sm font-semibold text-[#85464f] underline underline-offset-4">Browse everything</a>
            </div>
            @if ($menu->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($menu as $index => $category)
                        <a href="{{ route('category', $category) }}" class="group rounded-2xl border border-[#eae2da] bg-white px-4 py-5 transition hover:-translate-y-1 hover:border-[#d9b7b3] hover:shadow-md">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#f8eeec] text-lg text-[#a95f68]">{{ ['✳', '♡', '✦', '❋', '◌', '✿'][$index % 6] }}</span>
                            <span class="mt-4 block text-sm font-semibold group-hover:text-[#85464f]">{{ $category->name }}</span>
                            <span class="mt-1 block text-xs text-[#82766d]">Explore collection →</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="soft-card p-6 text-sm leading-6 text-[#82766d]">
                    Our collections are being prepared. Please check back soon.
                </div>
            @endif
        </section>

        <section>
            <div class="mb-6"><p class="eyebrow">Popular paths through the store</p><h2 class="section-title mt-2">Trending categories</h2></div>
            @if ($trendingCategories->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($trendingCategories as $index => $category)
                        <a href="{{ route('category', $category) }}" class="group flex min-h-36 flex-col justify-between rounded-2xl border border-[#eae2da] bg-white p-4 transition hover:-translate-y-1 hover:border-[#d9b7b3] hover:shadow-md sm:min-h-40 sm:p-5">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#f8eeec] text-lg text-[#a95f68]">{{ ['✳', '♡', '✦', '❋', '◌', '✿'][$index % 6] }}</span>
                            <span class="mt-5">
                                <span class="block font-semibold group-hover:text-[#85464f]">{{ $category->name }}</span>
                                <span class="mt-1 block text-xs text-[#82766d]">Shop the collection →</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="soft-card p-5 text-sm leading-6 text-[#82766d]">Trending categories are coming soon. Browse all our collections in the meantime.</p>
            @endif
        </section>

        <section class="rounded-[2rem] bg-[#f3e9e4] p-6 sm:p-10">
                <p class="eyebrow">Find products for your routine</p>
                <h2 class="section-title mt-2">Shop by concern</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#75645d]">Choose what you’re looking for and see products selected for that concern.</p>
                <div class="mt-7 space-y-8">
                    @foreach (['face' => 'Face', 'skin' => 'Skin', 'hair' => 'Hair'] as $type => $label)
                        @if (($concerns[$type] ?? collect())->isNotEmpty())
                            <div>
                                <h3 class="mb-4 text-lg font-semibold">{{ $label }}</h3>
                                <div class="grid gap-4 lg:grid-cols-2">
                                    @foreach ($concerns[$type] as $concern)
                                        <article class="min-w-0 rounded-2xl border border-white/80 bg-white/75 p-4 sm:p-5">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <h4 class="font-semibold">{{ $concern->name }}</h4>
                                                <a href="{{ route('search', ['filter[concern]' => $concern->slug]) }}" class="text-xs font-semibold text-[#85464f] underline underline-offset-4">See all matched products</a>
                                            </div>
                                            @if ($concern->products->isNotEmpty())
                                                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                                    @foreach ($concern->products->take(3) as $product)
                                                        <x-product-card :product="$product" />
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="mt-3 rounded-xl bg-[#faf7f2] px-4 py-3 text-sm leading-6 text-[#82766d]">
                                                    We’re selecting products for this concern. Check back soon.
                                                </p>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @if ($concerns->isEmpty())
                    <p class="mt-6 rounded-xl bg-white/75 p-4 text-sm leading-6 text-[#82766d]">Concern guides are being prepared. Please check back soon.</p>
                @endif
        </section>

        <section>
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div><p class="eyebrow">Just landed</p><h2 class="section-title mt-2">New arrivals</h2></div>
                    <a href="{{ route('search', ['sort' => '-created_at']) }}" class="text-sm font-semibold text-[#85464f] underline underline-offset-4">See all</a>
                </div>
                @if ($newArrivals->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($newArrivals as $product) <x-product-card :product="$product" /> @endforeach
                    </div>
                @else
                    <p class="soft-card p-5 text-sm leading-6 text-[#82766d]">Our latest arrivals will appear here as soon as they’re added.</p>
                @endif
        </section>

        @if ($topBrands->isNotEmpty())
            <section>
                <div class="mb-6"><p class="eyebrow">Names to know</p><h2 class="section-title mt-2">Loved brands</h2></div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($topBrands as $brand)
                        <a href="{{ route('brand', $brand) }}" class="group overflow-hidden rounded-2xl border border-[#eae2da] bg-white transition hover:-translate-y-1 hover:shadow-md">
                            @if ($brand->getFirstMediaUrl('tile'))
                                <img src="{{ $brand->getFirstMediaUrl('tile') }}" alt="{{ $brand->name }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                            @else
                                <div class="flex aspect-[4/3] items-center justify-center bg-[#f8eeec] px-4 text-center text-lg font-semibold text-[#85464f]">{{ $brand->name }}</div>
                            @endif
                            <div class="px-4 py-3"><p class="text-sm font-semibold">{{ $brand->name }}</p>@if ($brand->offer_label)<p class="mt-1 text-xs text-[#a95f68]">{{ $brand->offer_label }}</p>@endif</div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section>
                <div class="mb-6"><p class="eyebrow">A little more for less</p><h2 class="section-title mt-2">Beauty sets &amp; bundles</h2></div>
                @if ($combos->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($combos as $product) <x-product-card :product="$product" /> @endforeach
                    </div>
                @else
                    <p class="soft-card p-5 text-sm leading-6 text-[#82766d]">Beauty sets and bundles are coming soon.</p>
                @endif
        </section>

        <section>
                <div class="mb-6"><p class="eyebrow">Chosen by our team</p><h2 class="section-title mt-2">Recommended for you</h2></div>
                @if ($recommended->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($recommended as $product) <x-product-card :product="$product" /> @endforeach
                    </div>
                @else
                    <p class="soft-card p-5 text-sm leading-6 text-[#82766d]">Our team’s product picks will appear here soon.</p>
                @endif
        </section>

        <section class="rounded-[2rem] bg-[#f3e9e4] p-6 sm:p-10">
                <div class="mb-6"><p class="eyebrow">A little treat for your routine</p><h2 class="section-title mt-2">Special offers</h2></div>
                @if ($discounts->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($discounts as $product) <x-product-card :product="$product" /> @endforeach
                    </div>
                @else
                    <p class="rounded-xl bg-white/75 p-5 text-sm leading-6 text-[#82766d]">Discounted products will be listed here when offers are available.</p>
                @endif
        </section>

        <section>
                <div class="mb-6"><p class="eyebrow">Real words from real customers</p><h2 class="section-title mt-2">Our customers recommend</h2></div>
                @if ($reviews->isNotEmpty())
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($reviews as $review)
                            <article class="flex h-full flex-col rounded-2xl border border-[#eae2da] bg-white p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold">{{ $review->reviewer_name ?: 'Verified customer' }}</p>
                                <span class="text-sm tracking-widest text-[#a95f68]" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            <blockquote class="mt-4 flex-1 text-sm leading-7 text-[#655a52]">“{{ $review->body }}”</blockquote>
                            @if ($review->product)
                                <a href="{{ route('product', $review->product) }}" class="mt-5 flex items-center gap-3 border-t border-[#eee7e0] pt-4">
                                    @if ($review->product->getFirstMediaUrl('images', 'thumb'))
                                        <img src="{{ $review->product->getFirstMediaUrl('images', 'thumb') }}" alt="" loading="lazy" class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#f8eeec] text-[#a95f68]" aria-hidden="true">✿</span>
                                    @endif
                                    <span class="min-w-0">
                                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-[#a95f68]">Recommends</span>
                                        <span class="mt-1 block line-clamp-2 text-xs font-medium text-[#302823]">{{ $review->product->name }}</span>
                                    </span>
                                    <span class="ml-auto text-[#85464f]" aria-hidden="true">→</span>
                                </a>
                            @endif
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="soft-card p-5 text-sm leading-6 text-[#82766d]">Customer recommendations will appear here as reviews are added.</p>
                @endif
        </section>

        @if ($handpicked->isNotEmpty())
            <section>
                <div class="mb-6"><p class="eyebrow">Picked by us</p><h2 class="section-title mt-2">A few favourites</h2></div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($handpicked as $brand)
                        <a href="{{ route('brand', $brand) }}" class="group overflow-hidden rounded-2xl border border-[#eae2da] bg-white transition hover:-translate-y-1 hover:shadow-md">
                            @if ($brand->getFirstMediaUrl('tile'))
                                <img src="{{ $brand->getFirstMediaUrl('tile') }}" alt="{{ $brand->name }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                            @else
                                <div class="flex aspect-[4/3] items-center justify-center bg-[#f8eeec] px-4 text-center text-lg font-semibold text-[#85464f]">{{ $brand->name }}</div>
                            @endif
                            <div class="px-4 py-3"><p class="text-sm font-semibold">{{ $brand->name }}</p>@if ($brand->offer_label)<p class="mt-1 text-xs text-[#a95f68]">{{ $brand->offer_label }}</p>@endif</div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($ingredients->isNotEmpty())
            <section>
                <div class="mb-6"><p class="eyebrow">Know what goes into your routine</p><h2 class="section-title mt-2">Explore by ingredient</h2></div>
                <div class="grid gap-5 md:grid-cols-[minmax(12rem,0.7fr)_1.3fr]">
                    <div class="flex gap-2 overflow-x-auto pb-2 md:block md:space-y-2 md:pb-0" role="tablist" aria-label="Choose an ingredient" x-data="{ selectedIngredient: 0 }">
                        @foreach ($ingredients as $index => $ingredient)
                            <button type="button" role="tab" @click="$dispatch('ingredient-selected', {{ $index }})"
                                    class="whitespace-nowrap rounded-full border border-[#eae2da] bg-white px-4 py-2 text-left text-sm font-medium hover:border-[#cba5a0] md:block md:w-full md:rounded-xl">
                                {{ $ingredient->name }}
                            </button>
                        @endforeach
                    </div>
                    <div x-data="{ selected: 0 }" @ingredient-selected.window="selected = $event.detail" class="soft-card min-h-40 p-6 sm:p-8">
                        @foreach ($ingredients as $index => $ingredient)
                            <div x-show="selected === {{ $index }}" x-cloak>
                                <p class="eyebrow">{{ $ingredient->name }}</p>
                                <p class="mt-3 max-w-2xl leading-7 text-[#75645d]">{{ $ingredient->blurb ?: 'Discover products featuring '.$ingredient->name.'.' }}</p>
                                <a href="{{ route('search', ['filter[ingredient]' => $ingredient->slug]) }}" class="mt-5 inline-flex text-sm font-semibold text-[#85464f] underline underline-offset-4">Shop {{ $ingredient->name }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
