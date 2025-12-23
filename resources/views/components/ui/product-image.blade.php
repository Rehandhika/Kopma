@props([
    'src' => null,
    'alt' => '',
    'size' => 'medium', // thumbnail, medium, large
    'fallback' => true,
    'rounded' => 'lg',
    'aspectRatio' => 'square',
])

@php
    $sizeClasses = match($size) {
        'thumbnail' => 'w-12 h-12',
        'small' => 'w-16 h-16',
        'medium' => 'w-24 h-24',
        'large' => 'w-32 h-32',
        'xl' => 'w-48 h-48',
        'full' => 'w-full',
        default => $size,
    };

    $aspectClasses = match($aspectRatio) {
        'square' => 'aspect-square',
        'landscape' => 'aspect-video',
        'portrait' => 'aspect-[3/4]',
        default => '',
    };

    $roundedClasses = match($rounded) {
        'none' => '',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
        default => 'rounded-lg',
    };
@endphp

<div {{ $attributes->merge(['class' => "{$sizeClasses} {$aspectClasses} {$roundedClasses} bg-gray-100 overflow-hidden flex-shrink-0"]) }}>
    @if($src)
        <img 
            src="{{ $src }}" 
            alt="{{ $alt }}"
            class="w-full h-full object-cover"
            loading="lazy"
            decoding="async"
            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><svg class=\'w-1/3 h-1/3 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>';"
        >
    @elseif($fallback)
        <div class="w-full h-full flex items-center justify-center">
            <svg class="w-1/3 h-1/3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    @endif
</div>
