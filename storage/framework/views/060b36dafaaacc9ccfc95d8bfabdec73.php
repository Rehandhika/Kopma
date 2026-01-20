<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Notifikasi</h2>
            <p class="text-sm text-gray-500 mt-1"><?php echo e($stats['unread']); ?> notifikasi belum dibaca</p>
        </div>
        <div class="flex items-center gap-2">
            <!--[if BLOCK]><![endif]--><?php if($stats['unread'] > 0): ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['wire:click' => 'markAllAsRead','variant' => 'white','size' => 'sm','icon' => 'check-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'markAllAsRead','variant' => 'white','size' => 'sm','icon' => 'check-circle']); ?>
                    Tandai Semua Dibaca
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if($stats['total'] > 0): ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['wire:click' => 'deleteAll','wire:confirm' => 'Yakin ingin menghapus semua notifikasi?','variant' => 'white','size' => 'sm','icon' => 'trash','class' => 'text-red-600 hover:text-red-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'deleteAll','wire:confirm' => 'Yakin ingin menghapus semua notifikasi?','variant' => 'white','size' => 'sm','icon' => 'trash','class' => 'text-red-600 hover:text-red-700']); ?>
                    Hapus Semua
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- Filters -->
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['padding' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['padding' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
        <div class="p-4">
            <div class="flex flex-wrap gap-2">
                <button 
                    wire:click="$set('filter', 'all')" 
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                        'bg-indigo-100 text-indigo-700' => $filter === 'all',
                        'text-gray-700 hover:bg-gray-100' => $filter !== 'all',
                    ]); ?>"
                >
                    Semua (<?php echo e($stats['total']); ?>)
                </button>
                <button 
                    wire:click="$set('filter', 'unread')" 
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                        'bg-indigo-100 text-indigo-700' => $filter === 'unread',
                        'text-gray-700 hover:bg-gray-100' => $filter !== 'unread',
                    ]); ?>"
                >
                    Belum Dibaca (<?php echo e($stats['unread']); ?>)
                </button>
                <button 
                    wire:click="$set('filter', 'read')" 
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                        'bg-indigo-100 text-indigo-700' => $filter === 'read',
                        'text-gray-700 hover:bg-gray-100' => $filter !== 'read',
                    ]); ?>"
                >
                    Sudah Dibaca
                </button>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

    <!-- Notifications List -->
    <div class="space-y-3">
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['padding' => false,'class' => \Illuminate\Support\Arr::toCssClasses([
                    'hover:shadow-lg transition-shadow',
                    'opacity-75' => $notification->read_at,
                ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['padding' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([
                    'hover:shadow-lg transition-shadow',
                    'opacity-75' => $notification->read_at,
                ]))]); ?>
                <div class="p-4 sm:p-5">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'w-10 h-10 rounded-full flex items-center justify-center',
                                'bg-green-100' => $notification->type === 'success',
                                'bg-yellow-100' => $notification->type === 'warning',
                                'bg-red-100' => $notification->type === 'error',
                                'bg-blue-100' => !in_array($notification->type, ['success', 'warning', 'error']),
                            ]); ?>">
                                <?php
                                    $iconMap = [
                                        'success' => 'check-circle',
                                        'warning' => 'exclamation-triangle',
                                        'error' => 'x-circle',
                                        'info' => 'information-circle',
                                    ];
                                    $iconName = $iconMap[$notification->type] ?? 'information-circle';
                                    $iconColorMap = [
                                        'success' => 'text-green-600',
                                        'warning' => 'text-yellow-600',
                                        'error' => 'text-red-600',
                                        'info' => 'text-blue-600',
                                    ];
                                    $iconColor = $iconColorMap[$notification->type] ?? 'text-blue-600';
                                ?>
                                <?php if (isset($component)) { $__componentOriginal56804098dcf376a0e2227cb77b6cd00a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon','data' => ['name' => $iconName,'class' => 'w-5 h-5 '.e($iconColor).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconName),'class' => 'w-5 h-5 '.e($iconColor).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $attributes = $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $component = $__componentOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-medium text-gray-900"><?php echo e($notification->title); ?></h3>
                                        <?php
                                            $badgeVariantMap = [
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'error' => 'danger',
                                                'info' => 'info',
                                            ];
                                            $badgeVariant = $badgeVariantMap[$notification->type] ?? 'info';
                                        ?>
                                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $badgeVariant,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeVariant),'size' => 'sm']); ?>
                                            <?php echo e(ucfirst($notification->type)); ?>

                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo e($notification->message); ?></p>
                                    <p class="text-xs text-gray-400 mt-2"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <!--[if BLOCK]><![endif]--><?php if(!$notification->read_at): ?>
                                        <button 
                                            wire:click="markAsRead(<?php echo e($notification->id); ?>)" 
                                            class="text-indigo-600 hover:text-indigo-800 transition-colors p-1 rounded-lg hover:bg-indigo-50"
                                            title="Tandai sudah dibaca"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal56804098dcf376a0e2227cb77b6cd00a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon','data' => ['name' => 'check-circle','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $attributes = $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $component = $__componentOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <button 
                                        wire:click="delete(<?php echo e($notification->id); ?>)" 
                                        wire:confirm="Yakin ingin menghapus notifikasi ini?"
                                        class="text-red-600 hover:text-red-800 transition-colors p-1 rounded-lg hover:bg-red-50"
                                        title="Hapus"
                                    >
                                        <?php if (isset($component)) { $__componentOriginal56804098dcf376a0e2227cb77b6cd00a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon','data' => ['name' => 'trash','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $attributes = $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $component = $__componentOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Link if exists -->
                            <!--[if BLOCK]><![endif]--><?php if($notification->link): ?>
                                <a 
                                    href="<?php echo e($notification->link); ?>" 
                                    class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mt-3 font-medium transition-colors"
                                >
                                    Lihat Detail
                                    <?php if (isset($component)) { $__componentOriginal56804098dcf376a0e2227cb77b6cd00a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon','data' => ['name' => 'arrow-right','class' => 'w-4 h-4 ml-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'w-4 h-4 ml-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $attributes = $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__attributesOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a)): ?>
<?php $component = $__componentOriginal56804098dcf376a0e2227cb77b6cd00a; ?>
<?php unset($__componentOriginal56804098dcf376a0e2227cb77b6cd00a); ?>
<?php endif; ?>
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (isset($component)) { $__componentOriginalfe16eb12133e72aabae529d081318460 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe16eb12133e72aabae529d081318460 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.empty-state','data' => ['icon' => 'bell','title' => $filter === 'unread' ? 'Tidak ada notifikasi belum dibaca' : ($filter === 'read' ? 'Tidak ada notifikasi yang sudah dibaca' : 'Tidak ada notifikasi'),'description' => 'Notifikasi akan muncul di sini ketika ada aktivitas baru']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bell','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filter === 'unread' ? 'Tidak ada notifikasi belum dibaca' : ($filter === 'read' ? 'Tidak ada notifikasi yang sudah dibaca' : 'Tidak ada notifikasi')),'description' => 'Notifikasi akan muncul di sini ketika ada aktivitas baru']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe16eb12133e72aabae529d081318460)): ?>
<?php $attributes = $__attributesOriginalfe16eb12133e72aabae529d081318460; ?>
<?php unset($__attributesOriginalfe16eb12133e72aabae529d081318460); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe16eb12133e72aabae529d081318460)): ?>
<?php $component = $__componentOriginalfe16eb12133e72aabae529d081318460; ?>
<?php unset($__componentOriginalfe16eb12133e72aabae529d081318460); ?>
<?php endif; ?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Pagination -->
    <!--[if BLOCK]><![endif]--><?php if($notifications->hasPages()): ?>
        <?php if (isset($component)) { $__componentOriginal49b03e4fe6969761a6e8370f5d162f5c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49b03e4fe6969761a6e8370f5d162f5c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.pagination','data' => ['paginator' => $notifications]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifications)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49b03e4fe6969761a6e8370f5d162f5c)): ?>
<?php $attributes = $__attributesOriginal49b03e4fe6969761a6e8370f5d162f5c; ?>
<?php unset($__attributesOriginal49b03e4fe6969761a6e8370f5d162f5c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49b03e4fe6969761a6e8370f5d162f5c)): ?>
<?php $component = $__componentOriginal49b03e4fe6969761a6e8370f5d162f5c; ?>
<?php unset($__componentOriginal49b03e4fe6969761a6e8370f5d162f5c); ?>
<?php endif; ?>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\laragon\www\Kopma\resources\views/livewire/notification/index.blade.php ENDPATH**/ ?>