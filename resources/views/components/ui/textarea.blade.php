@props([
    'label' => null,
    'name' => '',
    'placeholder' => '',
    'rows' => 3,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
])

<div class="space-y-1">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge([
            'class' => implode(' ', [
                'block w-full rounded-lg border shadow-sm transition-colors duration-200',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
                'disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed',
                'px-3 py-2 text-sm',
                $error 
                    ? 'border-red-300 text-red-900 placeholder-danger-300 focus:border-red-500 focus:ring-danger-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
            ])
        ]) }}
    >{{ $slot }}</textarea>

    @if($help && !$error)
    <p class="text-xs text-gray-500">{{ $help }}</p>
    @endif

    @if($error)
    <p class="text-xs text-red-600 flex items-center">
        <x-ui.icon name="exclamation-circle" class="w-4 h-4 mr-1" />
        {{ $error }}
    </p>
    @endif
</div>
