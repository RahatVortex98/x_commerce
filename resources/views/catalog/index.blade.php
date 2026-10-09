@extends('layouts.shop', ['title' => $title.' | '.config('app.name')])

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
        <div class="rounded-[2rem] bg-[#f2e4df] px-6 py-8 sm:px-10 sm:py-12">
            <p class="eyebrow">A considered collection</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#493735] sm:text-5xl">{{ $title === 'Search' ? 'Find your next favourite' : $title }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-[#75645d]">
                {{ $title === 'Search' ? 'Search by product name, or browse our latest beauty and care essentials.' : 'Explore the collection and find something that feels right for you.' }}
            </p>
        </div>

        @if ($children->isNotEmpty())
            <section class="mt-8" aria-labelledby="subcategories-title">
                <h2 id="subcategories-title" class="text-sm font-semibold">Explore {{ $title }}</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($children as $child)
                        <a href="{{ route('category', $child) }}" class="rounded-full border border-[#eae2da] bg-white px-4 py-2 text-sm hover:border-[#cba5a0]">{{ $child->name }}</a>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="mt-9 flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-[#82766d]">
                @if ($products->total() === 1)
                    1 item
                @else
                    {{ $products->total() }} items
                @endif
            </p>
            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                @foreach (request()->except('sort', 'page') as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $nestedKey => $nestedValue)
                            <input type="hidden" name="{{ $key }}[{{ $nestedKey }}]" value="{{ $nestedValue }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="catalog-sort" class="text-sm text-[#82766d]">Sort by</label>
                <select id="catalog-sort" name="sort" onchange="this.form.submit()" class="rounded-full border-[#eae2da] bg-white py-2 pl-4 pr-9 text-sm">
                    <option value="-created_at" @selected(request('sort', '-created_at') === '-created_at')>Newest first</option>
                    <option value="price" @selected(request('sort') === 'price')>Price: low to high</option>
                    <option value="-price" @selected(request('sort') === '-price')>Price: high to low</option>
                    <option value="name" @selected(request('sort') === 'name')>Name: A to Z</option>
                </select>
            </form>
        </div>

        @if ($products->isNotEmpty())
            <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                @foreach ($products as $product) <x-product-card :product="$product" /> @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @else
            <div class="soft-card mt-5 px-6 py-12 text-center sm:py-16">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f8eeec] text-2xl text-[#a95f68]" aria-hidden="true">⌕</span>
                <h2 class="mt-4 text-xl font-semibold">Nothing here just yet</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#82766d]">We’re still adding products to this collection. Try another category or come back soon.</p>
                <a href="{{ route('home') }}" class="button-primary mt-6">Back to the home page</a>
            </div>
        @endif
    </div>
@endsection
