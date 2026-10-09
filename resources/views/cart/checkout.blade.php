@extends('layouts.shop', ['title' => 'Checkout | '.config('app.name')])

@section('content')
    @php
        [$subtotal, $dhakaShipping, $dhakaTotal] = $totals;
        $freeShipping = $subtotal >= config('shop.free_shipping_over');
    @endphp
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
        <a href="{{ route('cart') }}" class="text-sm font-medium text-[#85464f] underline underline-offset-4">← Back to your bag</a>
        <p class="eyebrow mt-6">Almost yours</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Delivery &amp; payment</h1>
        <p class="mt-3 text-sm text-[#82766d]">Enter your delivery details. We’ll confirm your order after you place it.</p>

        <form method="POST" action="{{ route('checkout.place') }}"
              x-data="{
                  zone: @js(old('zone', 'dhaka')),
                  subtotal: @js((float) $subtotal),
                  dhakaShipping: @js((float) config('shop.shipping_dhaka')),
                  outsideShipping: @js((float) config('shop.shipping_outside')),
                  freeShipping: @js($freeShipping),
                  currency: @js(config('shop.currency'))
              }"
              class="mt-8 grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_21rem]">
            @csrf
            <div class="space-y-5">
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 sm:p-7">
                    <h2 class="text-lg font-semibold">Where should we deliver?</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Use a phone number we can reach you on.</p>
                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="name" class="admin-label">Your name <span class="text-[#a95f68]">*</span></label>
                            <input id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" autocomplete="name" required class="form-control">
                            @error('name')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="admin-label">Phone number <span class="text-[#a95f68]">*</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="01XXXXXXXXX" required class="form-control">
                            <p class="admin-help">Enter a Bangladeshi mobile number, such as 01712345678.</p>
                            @error('phone')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="address" class="admin-label">Full delivery address <span class="text-[#a95f68]">*</span></label>
                            <textarea id="address" name="address" rows="4" autocomplete="street-address" placeholder="House, road, area and any delivery instructions" required class="form-control">{{ old('address') }}</textarea>
                            @error('address')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <fieldset>
                            <legend class="admin-label">Where are you located? <span class="text-[#a95f68]">*</span></legend>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#eee7e0] p-4">
                                    <input type="radio" name="zone" value="dhaka" x-model="zone" @checked(old('zone', 'dhaka') === 'dhaka') required class="mt-0.5 border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                                    <span><span class="block text-sm font-semibold">Inside Dhaka</span><span class="mt-1 block text-xs text-[#82766d]">{{ $freeShipping ? 'Free delivery' : config('shop.currency').number_format(config('shop.shipping_dhaka'), 2).' delivery' }}</span></span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#eee7e0] p-4">
                                    <input type="radio" name="zone" value="outside" x-model="zone" @checked(old('zone') === 'outside') required class="mt-0.5 border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                                    <span><span class="block text-sm font-semibold">Outside Dhaka</span><span class="mt-1 block text-xs text-[#82766d]">{{ $freeShipping ? 'Free delivery' : config('shop.currency').number_format(config('shop.shipping_outside'), 2).' delivery' }}</span></span>
                                </label>
                            </div>
                            @error('zone')<p class="admin-error">{{ $message }}</p>@enderror
                        </fieldset>
                    </div>
                </section>

                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 sm:p-7">
                    <h2 class="text-lg font-semibold">How would you like to pay?</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Choose a payment method for this order.</p>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach (['cod' => ['Cash on delivery', 'Pay when your order arrives.'], 'bkash' => ['bKash', 'Pay with bKash.'], 'nagad' => ['Nagad', 'Pay with Nagad.'], 'sslcommerz' => ['Card or online banking', 'Pay securely through SSLCommerz.']] as $method => $copy)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#eee7e0] p-4">
                                <input type="radio" name="payment_method" value="{{ $method }}" @checked(old('payment_method', 'cod') === $method) required class="mt-0.5 border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                                <span><span class="block text-sm font-semibold">{{ $copy[0] }}</span><span class="mt-1 block text-xs leading-5 text-[#82766d]">{{ $copy[1] }}</span></span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-4 rounded-xl border border-[#f0d9b0] bg-[#fff9ed] px-4 py-3 text-xs leading-5 text-[#765c2b]">
                        Online payment gateways are not connected yet. If you choose bKash, Nagad or card, your order is recorded but payment is not processed automatically.
                    </p>
                    @error('payment_method')<p class="admin-error">{{ $message }}</p>@enderror
                </section>
            </div>

            <aside class="rounded-2xl border border-[#eae2da] bg-white p-5 sm:p-6">
                <h2 class="text-lg font-semibold">Your order</h2>
                <ul class="mt-4 divide-y divide-[#eee7e0]">
                    @foreach ($lines as $line)
                        <li class="flex justify-between gap-3 py-3 text-sm">
                            <span class="text-[#655a52]">{{ $line->product->name }}@if ($line->variant) <span class="text-[#82766d]">· {{ $line->variant->name }}</span>@endif <span class="text-[#82766d]">× {{ $line->qty }}</span></span>
                            <span class="shrink-0 font-medium">{{ config('shop.currency') }}{{ number_format($line->total, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3 space-y-3 border-t border-[#eee7e0] pt-4 text-sm">
                    <div class="flex justify-between"><span class="text-[#82766d]">Subtotal</span><span>{{ config('shop.currency') }}{{ number_format($subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-[#82766d]">Delivery</span><span x-text="freeShipping ? 'Free' : currency + (zone === 'dhaka' ? dhakaShipping : outsideShipping).toFixed(2)">{{ $freeShipping ? 'Free' : config('shop.currency').number_format($dhakaShipping, 2) }}</span></div>
                </div>
                <p class="mt-3 text-xs leading-5 text-[#82766d]">Delivery is free for orders over {{ config('shop.currency') }}{{ number_format(config('shop.free_shipping_over'), 2) }}.</p>
                <div class="mt-4 flex justify-between border-t border-[#eee7e0] pt-4 font-semibold">
                    <span>Estimated total</span>
                    <span x-text="currency + (subtotal + (freeShipping ? 0 : (zone === 'dhaka' ? dhakaShipping : outsideShipping))).toFixed(2)">{{ config('shop.currency') }}{{ number_format($dhakaTotal, 2) }}</span>
                </div>
                <button type="submit" class="button-primary mt-5 w-full">Place my order</button>
                <p class="mt-3 text-center text-xs leading-5 text-[#82766d]">By placing your order, you confirm your delivery and payment details.</p>
            </aside>
        </form>
    </div>
@endsection
