@extends('layouts.shop', ['title' => 'Your shopping bag | '.config('app.name')])

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
        <p class="eyebrow">Your picks</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Shopping bag</h1>

        @if ($lines->isEmpty())
            <div class="soft-card mt-8 px-6 py-14 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f8eeec] text-2xl text-[#a95f68]" aria-hidden="true">♡</span>
                <h2 class="mt-4 text-xl font-semibold">Your bag is waiting</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#82766d]">You haven’t added anything yet. Take your time and find something that feels right.</p>
                <a href="{{ route('home') }}" class="button-primary mt-6">Explore the store</a>
            </div>
        @else
            <div class="mt-8 grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_21rem]">
                <div class="space-y-3">
                    @foreach ($lines as $line)
                        <article class="flex gap-4 rounded-2xl border border-[#eae2da] bg-white p-4 sm:p-5">
                            <a href="{{ route('product', $line->product) }}" class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-[#f8eeec] sm:h-28 sm:w-28">
                                @if ($line->product->getFirstMediaUrl('images', 'thumb'))
                                    <img src="{{ $line->product->getFirstMediaUrl('images', 'thumb') }}" alt="{{ $line->product->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="flex h-full items-center justify-center text-3xl text-[#c58f83]" aria-hidden="true">✿</span>
                                @endif
                            </a>
                            <div class="flex min-w-0 flex-1 flex-col sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <div>
                                    @if ($line->product->brand)
                                        <p class="text-xs font-semibold uppercase tracking-wider text-[#a95f68]">{{ $line->product->brand->name }}</p>
                                    @endif
                                    <a href="{{ route('product', $line->product) }}" class="mt-1 block font-semibold hover:text-[#85464f]">{{ $line->product->name }}</a>
                                    @if ($line->variant)
                                        <p class="mt-1 text-sm text-[#82766d]">{{ $line->variant->name }}</p>
                                    @endif
                                    <p class="mt-2 text-sm text-[#655a52]">{{ config('shop.currency') }}{{ number_format($line->price, 2) }} each</p>
                                </div>
                                <div class="mt-4 flex items-end justify-between gap-3 sm:mt-0 sm:flex-col sm:items-end">
                                    <form method="POST" action="{{ route('cart.update', $line->key) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <label for="qty-{{ $line->key }}" class="text-xs text-[#82766d]">Qty</label>
                                        <input id="qty-{{ $line->key }}" name="qty" type="number" min="1" max="10" value="{{ $line->qty }}" onchange="this.form.submit()" class="w-16 rounded-lg border-[#eae2da] py-1.5 text-center text-sm">
                                    </form>
                                    <div class="text-right">
                                        <p class="font-semibold">{{ config('shop.currency') }}{{ number_format($line->total, 2) }}</p>
                                        <form method="POST" action="{{ route('cart.remove', $line->key) }}" class="mt-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-[#82766d] underline underline-offset-4 hover:text-red-700">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="rounded-2xl border border-[#eae2da] bg-white p-5 sm:p-6">
                    <h2 class="text-lg font-semibold">Order summary</h2>
                    <div class="mt-5 flex justify-between text-sm">
                        <span class="text-[#82766d]">Subtotal</span>
                        <span class="font-semibold">{{ config('shop.currency') }}{{ number_format($sub, 2) }}</span>
                    </div>
                    @if ($sub >= config('shop.free_shipping_over'))
                        <p class="mt-3 rounded-xl bg-[#f4f0e8] px-3 py-2 text-xs leading-5 text-[#655a52]">Your order qualifies for free delivery.</p>
                        <div class="mt-3 flex justify-between text-sm"><span class="text-[#82766d]">Delivery</span><span class="font-medium">Free</span></div>
                    @else
                        <p class="mt-3 text-xs leading-5 text-[#82766d]">Delivery fee is calculated for your area at checkout. Free delivery on orders over {{ config('shop.currency') }}{{ number_format(config('shop.free_shipping_over')) }}.</p>
                    @endif
                    <div class="mt-5 border-t border-[#eee7e0] pt-4">
                        <div class="flex justify-between"><span class="font-semibold">Total</span><span class="font-semibold">{{ config('shop.currency') }}{{ number_format($sub, 2) }}<span class="ml-1 text-xs font-normal text-[#82766d]">+ delivery if applicable</span></span></div>
                    </div>
                    <a href="{{ route('checkout') }}" class="button-primary mt-5 w-full">Continue to checkout</a>
                    <a href="{{ route('home') }}" class="button-secondary mt-3 w-full">Keep browsing</a>
                    <p class="mt-4 text-center text-xs text-[#82766d]">Cash on delivery and digital payments available.</p>
                </aside>
            </div>
        @endif
    </div>
@endsection
