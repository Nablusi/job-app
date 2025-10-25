<div x-cloak x-show="show" x-transition:enter="transition ease-out duration-1000"
    x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100">
    {{ $slot }}
</div>