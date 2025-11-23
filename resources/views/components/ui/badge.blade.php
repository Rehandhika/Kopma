@props([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => false,
])

@php
$variants = [
    'primary' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
    'secondary' => 'bg-green-100 text-green-800 border-green-200',
    'success' => 'bg-green-50 text-green-700 border-green-200',
    'danger' => 'bg-red-50 text-red-700 border-red-200',
    'warning' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'info' => 'bg-blue-50 text-blue-700 border-blue-200',
    'gray' => 'bg-gray-100 text-gray-700 border-gray-200',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-sm',
    'lg' => 'px-3 py-1.5 text-base',
];
@endphp

<span {{ $attributes->merge([
    'class' => implode(' ', [
        'inline-flex items-center font-medium border',
        $rounded ? 'rounded-full' : 'rounded-md',
        $variants[$variant],
        $sizes[$size],
    ])
]) }}>
    {{ $slot }}
</span>
