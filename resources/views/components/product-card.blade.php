@props(['product'])

<article class="group relative flex flex-col overflow-hidden rounded-2xl border border-[#eae2da] bg-white transition duration-200 hover:-translate-y-1 hover:border-[#d9b7b3] hover:shadow-lg">
    <a href="{{ route('product', $product) }}" class="relative block overflow-hidden bg-[#f8eeec]">
        @if ($product->discount_percent)
            <span class="absolute left-3 top-3 z-10 rounded-full bg-[#85464f] px-2.5 py-1 text-[11px] font-semibold text-white">
                Save {{ $product->discount_percent }}%
            </span>
        @elseif ($product->is_new)
            <span class="absolute left-3 top-3 z-10 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-[#85464f]">
                New
            </span>
        @endif
        @if ($product->getFirstMediaUrl('images', 'thumb'))
            <img src="{{ $product->getFirstMediaUrl('images', 'thumb') }}"
                 alt="{{ $product->name }}" loading="lazy"
                 class="aspect-square w-full object-cover transition duration-500 group-hover:scale-[1.03]">
        @elseif ($product->getFirstMediaUrl('images'))
            <img src="{{ $product->getFirstMediaUrl('images') }}"
                 alt="{{ $product->name }}" loading="lazy"
                 class="aspect-square w-full object-cover transition duration-500 group-hover:scale-[1.03]">
        @else
            <div class="flex aspect-square flex-col items-center justify-center px-5 text-center">
                <span class="text-4xl text-[#c58f83]" aria-hidden="true">✿</span>
                <span class="mt-2 text-xs font-medium text-[#82766d]">Photo coming soon</span>
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-3.5 sm:p-4">
        @if ($product->brand)
            <a href="{{ route('brand', $product->brand) }}" class="text-[11px] font-semibold uppercase tracking-[0.12em] text-[#a95f68] hover:underline">
                {{ $product->brand->name }}
            </a>
        @endif
        <a href="{{ route('product', $product) }}" class="mt-1.5 line-clamp-2 min-h-10 text-sm font-semibold leading-5 text-[#302823] group-hover:text-[#85464f]">
            {{ $product->name }}
        </a>

        <div class="mt-3 flex flex-wrap items-baseline gap-x-2 gap-y-1">
            <span class="font-semibold text-[#302823]">{{ config('shop.currency') }}{{ number_format($product->price, 2) }}</span>
            @if ($product->compare_price > $product->price)
                <span class="text-xs text-[#968981] line-through">{{ config('shop.currency') }}{{ number_format($product->compare_price, 2) }}</span>
            @endif
        </div>

        <div class="mt-auto pt-4">
            @if (! $product->in_stock)
                <span class="block rounded-full bg-[#f1eeeb] px-3 py-2 text-center text-xs font-semibold text-[#82766d]">Out of stock</span>
            @elseif ($product->variants->isNotEmpty())
                <a href="{{ route('product', $product) }}" class="button-secondary w-full !py-2.5 !text-xs">Choose size or shade</a>
            @else
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="button-primary w-full !py-2.5 !text-xs">Add to bag</button>
                </form>
            @endif
        </div>
    </div>
</article>
