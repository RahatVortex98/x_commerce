@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border-[#eae2da] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#c87983] focus:ring-[#f1d5d7]']) }}>
