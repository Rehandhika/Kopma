<div class="space-y-4 sm:space-y-6">
    
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Laporan Penalti</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
            <?php echo e(\Carbon\Carbon::parse($dateFrom)->translatedFormat('d M Y')); ?> - 
            <?php echo e(\Carbon\Carbon::parse($dateTo)->translatedFormat('d M Y')); ?>

        </p>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700 space-y-3">
        
        <div class="flex flex-wrap gap-2">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ['today' => 'Hari Ini', 'week' => 'Minggu', 'month' => 'Bulan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button wire:click="setPeriod('<?php echo e($key); ?>')"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-3 py-1.5 text-xs sm:text-sm font-medium rounded-lg transition',
                        'bg-primary-600 text-white' => $period === $key,
                        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300' => $period !== $key,
                    ]); ?>">
                    <?php echo e($label); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
            <input type="date" wire:model.live.debounce.500ms="dateFrom" 
                class="px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <input type="date" wire:model.live.debounce.500ms="dateTo" 
                class="px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            
            <?php if (isset($component)) { $__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dropdown-select','data' => ['wire' => 'userFilter','options' => array_merge(
                    [['value' => 'all', 'label' => 'Semua User']],
                    collect($this->users)->map(fn($u) => ['value' => (string)$u->id, 'label' => $u->name])->toArray()
                ),'placeholder' => 'Semua User','searchable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire' => 'userFilter','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(array_merge(
                    [['value' => 'all', 'label' => 'Semua User']],
                    collect($this->users)->map(fn($u) => ['value' => (string)$u->id, 'label' => $u->name])->toArray()
                )),'placeholder' => 'Semua User','searchable' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032)): ?>
<?php $attributes = $__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032; ?>
<?php unset($__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032)): ?>
<?php $component = $__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032; ?>
<?php unset($__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032); ?>
<?php endif; ?>
            
            <?php if (isset($component)) { $__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dropdown-select','data' => ['wire' => 'statusFilter','options' => [
                    ['value' => 'all', 'label' => 'Semua Status'],
                    ['value' => 'active', 'label' => 'Aktif'],
                    ['value' => 'appealed', 'label' => 'Banding'],
                    ['value' => 'dismissed', 'label' => 'Dibatalkan'],
                    ['value' => 'expired', 'label' => 'Kadaluarsa'],
                ],'placeholder' => 'Semua Status']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire' => 'statusFilter','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['value' => 'all', 'label' => 'Semua Status'],
                    ['value' => 'active', 'label' => 'Aktif'],
                    ['value' => 'appealed', 'label' => 'Banding'],
                    ['value' => 'dismissed', 'label' => 'Dibatalkan'],
                    ['value' => 'expired', 'label' => 'Kadaluarsa'],
                ]),'placeholder' => 'Semua Status']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032)): ?>
<?php $attributes = $__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032; ?>
<?php unset($__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032)): ?>
<?php $component = $__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032; ?>
<?php unset($__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032); ?>
<?php endif; ?>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-red-100 text-xs font-medium">Total Penalti</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e(number_format($this->stats->total ?? 0)); ?></p>
            <p class="text-red-200 text-xs mt-0.5">kasus</p>
        </div>
        
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-amber-100 text-xs font-medium">Aktif</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e(number_format($this->stats->active ?? 0)); ?></p>
            <p class="text-amber-200 text-xs mt-0.5">penalti</p>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-blue-100 text-xs font-medium">Banding</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e(number_format($this->stats->appealed ?? 0)); ?></p>
            <p class="text-blue-200 text-xs mt-0.5">pengajuan</p>
        </div>
        
        <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-violet-100 text-xs font-medium">Total Poin</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e(number_format($this->stats->total_points ?? 0)); ?></p>
            <p class="text-violet-200 text-xs mt-0.5">poin penalti</p>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Detail Penalti</h3>
            <span class="text-xs text-gray-500"><?php echo e($penalties->total()); ?> data</span>
        </div>

        
        <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $penalties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penalty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $statusConfig = match($penalty->status) {
                        'active' => ['label' => 'Aktif', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                        'appealed' => ['label' => 'Banding', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400'],
                        'dismissed' => ['label' => 'Dibatalkan', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'],
                        'expired' => ['label' => 'Kadaluarsa', 'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500'],
                        default => ['label' => $penalty->status, 'class' => 'bg-gray-100 text-gray-700']
                    };
                ?>
                <div class="p-3 space-y-1">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-sm text-gray-900 dark:text-white truncate"><?php echo e($penalty->user->name ?? '-'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($penalty->user->nim ?? '-'); ?></p>
                        </div>
                        <span class="px-1.5 py-0.5 rounded text-xs font-medium <?php echo e($statusConfig['class']); ?> shrink-0 ml-2">
                            <?php echo e($statusConfig['label']); ?>

                        </span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500"><?php echo e($penalty->date->format('d/m/Y')); ?></span>
                        <span class="font-semibold text-red-600 dark:text-red-400"><?php echo e($penalty->points); ?> poin</span>
                    </div>
                    <div class="text-xs">
                        <span class="font-medium text-gray-700 dark:text-gray-300"><?php echo e($penalty->penaltyType->name ?? '-'); ?></span>
                        <span class="text-gray-400">(<?php echo e($penalty->penaltyType->code ?? '-'); ?>)</span>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($penalty->description): ?>
                        <p class="text-xs text-gray-500 truncate"><?php echo e($penalty->description); ?></p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-8 text-center text-gray-400 text-sm">Tidak ada data penalti</div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Tanggal</th>
                        <th class="px-4 py-2.5 text-left">Nama</th>
                        <th class="px-4 py-2.5 text-left">Jenis</th>
                        <th class="px-4 py-2.5 text-center">Poin</th>
                        <th class="px-4 py-2.5 text-left">Deskripsi</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $penalties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penalty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $statusConfig = match($penalty->status) {
                                'active' => ['label' => 'Aktif', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                                'appealed' => ['label' => 'Banding', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400'],
                                'dismissed' => ['label' => 'Dibatalkan', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'],
                                'expired' => ['label' => 'Kadaluarsa', 'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500'],
                                default => ['label' => $penalty->status, 'class' => 'bg-gray-100 text-gray-700']
                            };
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400"><?php echo e($penalty->date->format('d/m/Y')); ?></td>
                            <td class="px-4 py-2.5">
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo e($penalty->user->name ?? '-'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($penalty->user->nim ?? '-'); ?></p>
                            </td>
                            <td class="px-4 py-2.5">
                                <p class="text-gray-900 dark:text-white"><?php echo e($penalty->penaltyType->name ?? '-'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($penalty->penaltyType->code ?? '-'); ?></p>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="font-semibold text-red-600 dark:text-red-400"><?php echo e($penalty->points); ?></span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                <?php echo e($penalty->description ?? '-'); ?>

                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-medium <?php echo e($statusConfig['class']); ?>">
                                    <?php echo e($statusConfig['label']); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">Tidak ada data penalti</td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <!--[if BLOCK]><![endif]--><?php if($penalties->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                <?php echo e($penalties->links()); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <div wire:loading.delay class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 shadow-lg flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-sm text-gray-700 dark:text-gray-300">Memuat...</span>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Kopma\resources\views/livewire/report/penalty-report.blade.php ENDPATH**/ ?>