<div class="space-y-4 sm:space-y-6">
    
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Penalti Saya</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Lihat dan kelola penalti yang Anda terima</p>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($this->summary['total_points'] >= 50): ?>
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 sm:p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-medium text-red-800 dark:text-red-200 text-sm">Peringatan: Total Poin Tinggi</p>
                    <p class="text-xs text-red-700 dark:text-red-300 mt-0.5">Anda memiliki <?php echo e($this->summary['total_points']); ?> poin penalti aktif.</p>
                </div>
            </div>
        </div>
    <?php elseif($this->summary['total_points'] >= 30): ?>
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 sm:p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-medium text-amber-800 dark:text-amber-200 text-sm">Perhatian: Poin Meningkat</p>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">Anda memiliki <?php echo e($this->summary['total_points']); ?> poin penalti aktif.</p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-red-100 text-xs font-medium">Total Poin</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e($this->summary['total_points']); ?></p>
            <p class="text-red-200 text-xs mt-0.5">poin aktif</p>
        </div>
        
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-amber-100 text-xs font-medium">Aktif</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e($this->summary['active']); ?></p>
            <p class="text-amber-200 text-xs mt-0.5">penalti</p>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-blue-100 text-xs font-medium">Banding</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e($this->summary['appealed']); ?></p>
            <p class="text-blue-200 text-xs mt-0.5">pengajuan</p>
        </div>
        
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg p-3 sm:p-4 text-white">
            <p class="text-gray-100 text-xs font-medium">Total</p>
            <p class="text-lg sm:text-xl font-bold mt-1"><?php echo e($this->summary['count']); ?></p>
            <p class="text-gray-200 text-xs mt-0.5">penalti</p>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700">
        <label class="block text-xs text-gray-500 mb-1.5">Filter Status</label>
        <?php if (isset($component)) { $__componentOriginal01b6cc14fe7fc10e17b29bd1c55b4032 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01b6cc14fe7fc10e17b29bd1c55b4032 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dropdown-select','data' => ['wire' => 'statusFilter','options' => [
                ['value' => '', 'label' => 'Semua Status'],
                ['value' => 'active', 'label' => 'Aktif'],
                ['value' => 'appealed', 'label' => 'Banding'],
                ['value' => 'dismissed', 'label' => 'Dibatalkan'],
                ['value' => 'expired', 'label' => 'Kedaluwarsa'],
            ],'placeholder' => 'Semua Status','class' => 'w-full sm:w-56']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire' => 'statusFilter','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                ['value' => '', 'label' => 'Semua Status'],
                ['value' => 'active', 'label' => 'Aktif'],
                ['value' => 'appealed', 'label' => 'Banding'],
                ['value' => 'dismissed', 'label' => 'Dibatalkan'],
                ['value' => 'expired', 'label' => 'Kedaluwarsa'],
            ]),'placeholder' => 'Semua Status','class' => 'w-full sm:w-56']); ?>
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

    
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Daftar Penalti</h3>
        </div>

        
        <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $penalties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penalty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $statusConfig = match($penalty->status) {
                        'active' => ['label' => 'Aktif', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'],
                        'appealed' => ['label' => 'Banding', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                        'dismissed' => ['label' => 'Dibatalkan', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'],
                        'expired' => ['label' => 'Kedaluwarsa', 'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500'],
                        default => ['label' => $penalty->status, 'class' => 'bg-gray-100 text-gray-700']
                    };
                ?>
                <div class="p-3 space-y-1">
                    <div class="flex justify-between items-start">
                        <span class="font-medium text-sm text-gray-900 dark:text-white"><?php echo e($penalty->penaltyType->name ?? '-'); ?></span>
                        <span class="px-1.5 py-0.5 rounded text-xs font-medium <?php echo e($statusConfig['class']); ?>">
                            <?php echo e($statusConfig['label']); ?>

                        </span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500"><?php echo e($penalty->date->format('d/m/Y')); ?></span>
                        <span class="font-semibold text-red-600 dark:text-red-400"><?php echo e($penalty->points); ?> poin</span>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($penalty->description): ?>
                        <p class="text-xs text-gray-500 truncate"><?php echo e($penalty->description); ?></p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-8 text-center text-gray-400 text-sm">Tidak ada penalti</div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Tanggal</th>
                        <th class="px-4 py-2.5 text-left">Jenis</th>
                        <th class="px-4 py-2.5 text-left">Deskripsi</th>
                        <th class="px-4 py-2.5 text-center">Poin</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $penalties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penalty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $statusConfig = match($penalty->status) {
                                'active' => ['label' => 'Aktif', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'],
                                'appealed' => ['label' => 'Banding', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'],
                                'dismissed' => ['label' => 'Dibatalkan', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'],
                                'expired' => ['label' => 'Kedaluwarsa', 'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500'],
                                default => ['label' => $penalty->status, 'class' => 'bg-gray-100 text-gray-700']
                            };
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400"><?php echo e($penalty->date->format('d/m/Y')); ?></td>
                            <td class="px-4 py-2.5 text-gray-900 dark:text-white font-medium"><?php echo e($penalty->penaltyType->name ?? '-'); ?></td>
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400 max-w-xs truncate"><?php echo e($penalty->description ?? '-'); ?></td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="font-semibold text-red-600 dark:text-red-400"><?php echo e($penalty->points); ?></span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-medium <?php echo e($statusConfig['class']); ?>">
                                    <?php echo e($statusConfig['label']); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400 text-sm">Tidak ada penalti</td>
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
<?php /**PATH C:\laragon\www\Kopma\resources\views/livewire/penalty/index.blade.php ENDPATH**/ ?>