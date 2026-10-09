@extends('layouts.admin', ['title' => 'Overview'])

@section('content')
    <section class="mb-9 rounded-[2rem] bg-[#f2e4df] px-6 py-8 sm:px-10 sm:py-10">
        <p class="eyebrow">Your shop at a glance</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Welcome, {{ auth()->user()->name }}.</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-[#75645d]">
            This is where you add the things customers see. Start with a product, then give it a category and brand.
            Everything you save here updates your storefront.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('admin.products.create') }}" class="button-primary">＋ Add your first product</a>
            <a href="{{ route('admin.categories.create') }}" class="button-secondary">Create a category</a>
        </div>
    </section>

    <section aria-labelledby="content-counts-title">
        <div class="mb-4">
            <p class="eyebrow">Your content</p>
            <h2 id="content-counts-title" class="section-title mt-1">Store sections</h2>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                'products' => ['label' => 'Products', 'hint' => 'Items customers can buy'],
                'categories' => ['label' => 'Categories', 'hint' => 'How shoppers browse'],
                'brands' => ['label' => 'Brands', 'hint' => 'Brands you carry'],
                'banners' => ['label' => 'Homepage banners', 'hint' => 'Promotions on your home page'],
                'concerns' => ['label' => 'Shop by concern', 'hint' => 'Products matched to needs'],
                'reviews' => ['label' => 'Customer reviews', 'hint' => 'Approved customer feedback'],
            ] as $resource => $info)
                <a href="{{ route("admin.$resource.index") }}" class="group rounded-2xl border border-[#eae2da] bg-white p-5 transition hover:-translate-y-0.5 hover:border-[#d9b7b3] hover:shadow-md">
                    <span class="text-sm font-semibold">{{ $info['label'] }}</span>
                    <span class="mt-1 block text-xs text-[#82766d]">{{ $info['hint'] }}</span>
                    <span class="mt-5 block text-3xl font-semibold text-[#85464f]">{{ $counts[$resource] }}</span>
                    <span class="mt-3 block text-sm font-medium text-[#85464f] group-hover:underline">Manage {{ strtolower($info['label']) }} →</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mt-12" aria-labelledby="quick-start-title">
        <div class="mb-4">
            <p class="eyebrow">New to the dashboard?</p>
            <h2 id="quick-start-title" class="section-title mt-1">A simple way to get started</h2>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-2xl border border-[#eae2da] bg-white p-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#f8eeec] font-semibold text-[#85464f]">1</span>
                <h3 class="mt-4 font-semibold">Add a category</h3>
                <p class="mt-2 text-sm leading-6 text-[#82766d]">Categories help customers find products. For example: Skincare → Moisturizers.</p>
                <a href="{{ route('admin.categories.create') }}" class="mt-4 inline-block text-sm font-semibold text-[#85464f] underline underline-offset-4">Create category</a>
            </article>
            <article class="rounded-2xl border border-[#eae2da] bg-white p-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#f8eeec] font-semibold text-[#85464f]">2</span>
                <h3 class="mt-4 font-semibold">Add a brand, if needed</h3>
                <p class="mt-2 text-sm leading-6 text-[#82766d]">Add the maker’s name and an optional logo. Products can also be added without a brand.</p>
                <a href="{{ route('admin.brands.create') }}" class="mt-4 inline-block text-sm font-semibold text-[#85464f] underline underline-offset-4">Add brand</a>
            </article>
            <article class="rounded-2xl border border-[#eae2da] bg-white p-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#f8eeec] font-semibold text-[#85464f]">3</span>
                <h3 class="mt-4 font-semibold">Add a product</h3>
                <p class="mt-2 text-sm leading-6 text-[#82766d]">Add a name, price, stock and photo. Choose the category where shoppers should find it.</p>
                <a href="{{ route('admin.products.create') }}" class="mt-4 inline-block text-sm font-semibold text-[#85464f] underline underline-offset-4">Add product</a>
            </article>
        </div>
    </section>

    <section class="mt-8 rounded-2xl border border-[#eae2da] bg-white p-5 sm:p-6">
        <h2 class="font-semibold">Want to feature a promotion?</h2>
        <p class="mt-2 text-sm leading-6 text-[#82766d]">Add a homepage banner with a title, image and optional destination link. Keep the image wide for desktop; a separate mobile image is optional.</p>
        <a href="{{ route('admin.banners.create') }}" class="mt-4 inline-block text-sm font-semibold text-[#85464f] underline underline-offset-4">Create homepage banner</a>
    </section>
    <section class="mt-8 rounded-2xl border border-[#eae2da] bg-white p-5 sm:p-6">
        <h2 class="font-semibold">Match products to customer needs</h2>
        <p class="mt-2 text-sm leading-6 text-[#82766d]">Create concerns such as Acne, Hyperpigmentation or Sun Protection. Then edit each product and select the concerns it helps with.</p>
        <div class="mt-4 flex flex-wrap gap-4 text-sm font-semibold text-[#85464f]">
            <a href="{{ route('admin.concerns.index') }}" class="underline underline-offset-4">Manage concerns</a>
            <a href="{{ route('admin.reviews.index') }}" class="underline underline-offset-4">Manage customer reviews</a>
        </div>
    </section>
@endsection
