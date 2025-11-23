@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'loading' => false,
    'disabled' => false,
    'icon' => null,
    'href' => null,
])

@php
$variants = [
    'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500',
    'secondary' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
    'success' => 'bg-green-500 hover:bg-green-700 text-white focus:ring-green-500',
    'danger' => 'bg-red-500 hover:bg-red-700 text-white focus:ring-red-500',
    'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-500',
    'info' => 'bg-blue-500 hover:bg-blue-700 text-white focus:ring-blue-500',
    'white' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500',
    'outline' => 'bg-transparent border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 focus:ring-indigo-500',
    'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$isDisabled = $loading || $disabled;
@endphp

@if($href && !$isDisabled)
<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => implode(' ', [$baseClasses, $variants[$variant], $sizes[$size]])]) }}
>
    @if($loading)
        <x-ui.spinner class="mr-2" size="sm" color="white" />
    @elseif($icon)
        <x-ui.icon :name="$icon" class="mr-2 w-5 h-5" />
    @endif
    {{ $slot }}
</a>
@else
<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => implode(' ', [$baseClasses, $variants[$variant], $sizes[$size]])]) }}
    @if($isDisabled) disabled @endif
>
    @if($loading)
        <x-ui.spinner class="mr-2" size="sm" color="white" />
    @elseif($icon)
        <x-ui.icon :name="$icon" class="mr-2 w-5 h-5" />
    @endif
    {{ $slot }}
</button>
@endif
