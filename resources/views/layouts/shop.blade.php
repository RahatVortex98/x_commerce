<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#faf7f2">
    <title>{{ $title ?? config('app.name').' | Beauty, chosen with care' }}</title>
    <meta name="description" content="Discover thoughtful beauty and skincare, with delivery across Bangladesh.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-white focus:px-4 focus:py-2">
        Skip to content
    </a>

    <div class="bg-[#6f4147] px-4 py-2 text-center text-xs font-medium tracking-wide text-white sm:text-sm">
        Thoughtful beauty, delivered across Bangladesh
    </div>

    <header class="sticky top-0 z-40 border-b border-[#eae2da] bg-[#faf7f2]/95 backdrop-blur" x-data="{ mobileOpen: false }">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-4 sm:px-6 lg:gap-8">
            <button type="button" class="rounded-lg p-2 text-xl lg:hidden" @click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen.toString()" aria-controls="mobile-navigation" aria-label="Toggle navigation">
                <span aria-hidden="true" x-text="mobileOpen ? '×' : '☰'"></span>
            </button>
            <a href="{{ route('home') }}" class="shrink-0 text-xl font-semibold tracking-tight text-[#493735] sm:text-2xl">
                {{ config('app.name') }}
                <span class="mt-0.5 hidden text-[9px] font-medium uppercase tracking-[0.24em] text-[#a95f68] sm:block">Beauty, chosen with care</span>
            </a>

            <form action="{{ route('search') }}" role="search" class="order-3 w-full sm:order-none sm:mx-auto sm:max-w-md">
                <label for="site-search" class="sr-only">Search products</label>
                <div class="relative">
                    <input id="site-search" name="filter[name]" value="{{ request('filter.name') }}"
                           placeholder="Search products, brands…" class="form-control rounded-full !py-2.5 !pl-11">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#82766d]" aria-hidden="true">⌕</span>
                </div>
            </form>

            <div class="ml-auto flex shrink-0 items-center gap-3 sm:gap-5">
                @if (auth()->user()?->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="hidden text-sm font-medium text-[#6f4147] sm:inline">Admin</a>
                @endif
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="text-sm font-medium">
                    {{ auth()->check() ? 'Account' : 'Sign in' }}
                </a>
                <a href="{{ route('cart') }}" class="relative rounded-full border border-[#eae2da] bg-white px-3 py-2 text-sm font-semibold">
                    Bag <span class="text-[#a95f68]">({{ collect(session('cart', []))->sum('qty') }})</span>
                </a>
            </div>
        </div>

        <nav class="hidden border-t border-[#eae2da] lg:block" aria-label="Shop by category">
            <ul class="mx-auto flex max-w-7xl items-center justify-center gap-9 px-6 text-sm">
                @foreach ($menu as $top)
                    <li class="group relative">
                        <a href="{{ route('category', $top) }}" class="block py-3.5 font-medium hover:text-[#a95f68]">
                            {{ $top->name }}
                        </a>
                        @if ($top->children->isNotEmpty())
                            <div class="invisible absolute left-1/2 top-full z-50 w-[min(880px,90vw)] -translate-x-1/2 rounded-b-2xl border border-t-0 border-[#eae2da] bg-white p-7 opacity-0 shadow-xl transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                                <div class="grid grid-cols-4 gap-7">
                                    @foreach ($top->children as $group)
                                        <div>
                                            <a href="{{ route('category', $group) }}" class="font-semibold hover:text-[#a95f68]">{{ $group->name }}</a>
                                            <ul class="mt-3 space-y-2 text-sm text-[#82766d]">
                                                @foreach ($group->children as $leaf)
                                                    <li><a href="{{ route('category', $leaf) }}" class="hover:text-[#a95f68]">{{ $leaf->name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <nav id="mobile-navigation" x-show="mobileOpen" x-cloak class="max-h-[70vh] overflow-y-auto border-t border-[#eae2da] bg-white lg:hidden" aria-label="Mobile shop navigation">
            <ul class="divide-y divide-[#eee7e0] px-4">
                @foreach ($menu as $top)
                    <li>
                        <details class="py-3">
                            <summary class="cursor-pointer list-none font-semibold">{{ $top->name }} <span class="float-right text-[#a95f68]">＋</span></summary>
                            <a href="{{ route('category', $top) }}" class="mt-3 block text-sm font-medium text-[#a95f68]">Shop all {{ $top->name }}</a>
                            @foreach ($top->children as $group)
                                <a href="{{ route('category', $group) }}" class="ml-3 mt-3 block text-sm text-[#655a52]">{{ $group->name }}</a>
                                @foreach ($group->children as $leaf)
                                    <a href="{{ route('category', $leaf) }}" class="ml-6 mt-2 block text-sm text-[#82766d]">{{ $leaf->name }}</a>
                                @endforeach
                            @endforeach
                        </details>
                    </li>
                @endforeach
            </ul>
        </nav>
    </header>

    @if (session('ok'))
        <div role="status" class="border-b border-green-200 bg-green-50 px-4 py-3 text-center text-sm font-medium text-green-900">
            {{ session('ok') }}
        </div>
    @endif

    <main id="main-content">{{ $slot ?? '' }}@yield('content')</main>

    <footer class="mt-20 bg-[#342d29] text-[#f6eee8]">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <a href="{{ route('home') }}" class="text-xl font-semibold">{{ config('app.name') }}</a>
                <p class="mt-3 max-w-sm text-sm leading-6 text-[#d2c3b9]">A considered edit of beauty and care essentials. Find what feels right for you.</p>
            </div>
            <div>
                <h2 class="text-sm font-semibold">Here to help</h2>
                <p class="mt-3 text-sm leading-6 text-[#d2c3b9]">Delivery: Dhaka within 2 days. Outside Dhaka within 3–5 days.</p>
                <p class="mt-2 text-sm text-[#d2c3b9]">Contact details coming soon.</p>
            </div>
            <div>
                <h2 class="text-sm font-semibold">Shop with confidence</h2>
                <ul class="mt-3 space-y-2 text-sm text-[#d2c3b9]">
                    <li>Authentic products</li>
                    <li>Cash on delivery available</li>
                    <li>bKash · Nagad · SSLCommerz</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 px-4 py-4 text-center text-xs text-[#b8a79d]">
            © {{ date('Y') }} {{ config('app.name') }}. Made for your everyday rituals.
        </div>
    </footer>
</body>
</html>
