<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Your account' }} | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#faf7f2] text-[#302823] antialiased">
    <main class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10 sm:px-6">
        <div class="grid w-full overflow-hidden rounded-[2rem] border border-[#eae2da] bg-white shadow-sm lg:grid-cols-[.9fr_1.1fr]">
            <aside class="relative hidden min-h-[34rem] flex-col justify-between overflow-hidden bg-[#f2e4df] p-10 lg:flex">
                <div class="absolute -right-20 top-20 h-72 w-72 rounded-full bg-[#d5b4a9]/50 blur-2xl"></div>
                <a href="{{ route('home') }}" class="relative text-xl font-semibold tracking-tight text-[#493735]">{{ config('app.name') }}</a>
                <div class="relative max-w-sm pb-4">
                    <p class="eyebrow">Beauty, chosen with care</p>
                    <p class="mt-4 text-4xl font-semibold leading-tight tracking-tight text-[#493735]">A little space for the things you love.</p>
                    <p class="mt-4 text-sm leading-6 text-[#75645d]">Sign in to make shopping feel simple. Your everyday beauty and care essentials are right here.</p>
                </div>
                <p class="relative text-xs text-[#75645d]">Thoughtful beauty, delivered across Bangladesh.</p>
            </aside>
            <section class="p-6 sm:p-10 lg:p-12">
                <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-[#493735] lg:hidden">{{ config('app.name') }}</a>
                <div class="mx-auto mt-5 max-w-md lg:mt-10">
                    {{ $slot }}
                </div>
                <p class="mx-auto mt-8 max-w-md text-center text-xs text-[#82766d]">
                    <a href="{{ route('home') }}" class="underline underline-offset-4">Continue browsing the store</a>
                </p>
            </section>
        </div>
    </main>
</body>
</html>
