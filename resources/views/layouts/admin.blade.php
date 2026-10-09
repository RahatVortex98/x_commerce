<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#faf7f2">
    <title>{{ $title ?? 'Store dashboard' }} | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#faf7f2] text-[#302823] antialiased">
    <a href="#admin-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-white focus:px-4 focus:py-2">
        Skip to content
    </a>
    <header class="border-b border-[#eae2da] bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-8 gap-y-3 px-4 py-4 sm:px-6">
            <a href="{{ route('admin.dashboard') }}" class="mr-auto">
                <span class="block text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-[#a95f68]">Store dashboard</span>
            </a>
            <nav class="flex w-full gap-2 overflow-x-auto pb-1 sm:w-auto sm:pb-0" aria-label="Admin navigation">
                @foreach ([
                    'admin.dashboard' => 'Overview',
                    'admin.products.index' => 'Products',
                    'admin.categories.index' => 'Categories',
                    'admin.concerns.index' => 'Shop by concern',
                    'admin.brands.index' => 'Brands',
                    'admin.banners.index' => 'Homepage banners',
                    'admin.reviews.index' => 'Customer reviews',
                ] as $routeName => $label)
                    <a href="{{ route($routeName) }}"
                       @class([
                           'whitespace-nowrap rounded-full px-3 py-2 text-sm font-medium transition',
                           'bg-[#f8eeec] text-[#85464f]' => request()->routeIs($routeName) || ($routeName === 'admin.dashboard' && request()->routeIs('admin.dashboard')),
                           'text-[#655a52] hover:bg-[#faf7f2]' => ! (request()->routeIs($routeName) || ($routeName === 'admin.dashboard' && request()->routeIs('admin.dashboard'))),
                       ])>{{ $label }}</a>
                @endforeach
            </nav>
            <div class="flex w-full items-center gap-4 border-t border-[#eee7e0] pt-3 sm:w-auto sm:border-0 sm:pt-0">
                <a href="{{ route('home') }}" class="text-sm font-medium text-[#655a52] hover:text-[#85464f]">View storefront ↗</a>
                <span class="hidden text-sm text-[#82766d] sm:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-[#85464f] underline underline-offset-4">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    @if (session('ok'))
        <div role="status" class="border-b border-green-200 bg-green-50 px-4 py-3 text-center text-sm font-medium text-green-900">
            {{ session('ok') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="mx-auto mt-5 max-w-7xl rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-900">
            <p class="font-semibold">Please check the highlighted fields and try again.</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <main id="admin-content" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12">
        @yield('content')
    </main>
    <footer class="border-t border-[#eae2da] px-4 py-5 text-center text-xs text-[#82766d]">
        You’re managing {{ config('app.name') }}. Changes you save appear on the storefront.
    </footer>
</body>
</html>
