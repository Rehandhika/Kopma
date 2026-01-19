<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<span <?php echo e($attributes->merge([
    'class' => implode(' ', [
        'inline-flex items-center font-medium border',
        $rounded ? 'rounded-full' : 'rounded-md',
        $variants[$variant],
        $sizes[$size],
    ])
])); ?>>
    <?php echo e($slot); ?>

</span>
<?php /**PATH C:\laragon\www\Kopma\resources\views/components/ui/badge.blade.php ENDPATH**/ ?>