<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full border border-transparent bg-[#85464f] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#713b43] focus:outline-none focus:ring-2 focus:ring-[#c87983] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
