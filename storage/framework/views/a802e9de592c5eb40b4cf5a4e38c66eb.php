<div class="space-y-6">
    
    <?php if (isset($component)) { $__componentOriginal4743781065990dfe96029737c4f06097 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4743781065990dfe96029737c4f06097 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.page-header','data' => ['title' => 'Riwayat Absensi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Riwayat Absensi']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','wire:click' => 'export']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','wire:click' => 'export']); ?>
                <?php if (isset($component)) { $__componentOriginal56804098dcf376a0e2227cb77b6cd00a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon','data' => ['name' => 'download','class' => 'w-5 h-5 mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'download','class' => 'w-5 h-5 mr-2']); ?>
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
                Export Excel
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
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4743781065990dfe96029737c4f06097)): ?>
<?php $attributes = $__attributesOriginal4743781065990dfe96029737c4f06097; ?>
<?php unset($__attributesOriginal4743781065990dfe96029737c4f06097); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4743781065990dfe96029737c4f06097)): ?>
<?php $component = $__componentOriginal4743781065990dfe96029737c4f06097; ?>
<?php unset($__componentOriginal4743781065990dfe96029737c4f06097); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginalddfdd8eb2a2b0d47c29a0436357ad57f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalddfdd8eb2a2b0d47c29a0436357ad57f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.grid','data' => ['cols' => '4','gap' => '4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.grid'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => '4','gap' => '4']); ?>
            <div>
                <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['type' => 'date','name' => 'dateFrom','label' => 'Dari Tanggal','wire:model' => 'dateFrom']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'dateFrom','label' => 'Dari Tanggal','wire:model' => 'dateFrom']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
            </div>
            <div>
                <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['type' => 'date','name' => 'dateTo','label' => 'Sampai Tanggal','wire:model' => 'dateTo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'dateTo','label' => 'Sampai Tanggal','wire:model' => 'dateTo']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
            </div>
            <div>
                <?php if (isset($component)) { $__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select','data' => ['name' => 'status','label' => 'Status','wire:model.live' => 'status','options' => [
                        '' => 'Semua Status',
                        'present' => 'Hadir',
                        'late' => 'Terlambat',
                        'absent' => 'Tidak Hadir'
                    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','label' => 'Status','wire:model.live' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                        '' => 'Semua Status',
                        'present' => 'Hadir',
                        'late' => 'Terlambat',
                        'absent' => 'Tidak Hadir'
                    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862)): ?>
<?php $attributes = $__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862; ?>
<?php unset($__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862)): ?>
<?php $component = $__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862; ?>
<?php unset($__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862); ?>
<?php endif; ?>
            </div>
            <div class="flex items-end space-x-2">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'primary','class' => 'flex-1','wire:click' => 'applyFilter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','class' => 'flex-1','wire:click' => 'applyFilter']); ?>
                    <?php if (isset($component)) { $__componentOriginal56804098dcf376a0e2227cb77b6cd00a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon','data' => ['name' => 'filter','class' => 'w-5 h-5 mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'filter','class' => 'w-5 h-5 mr-2']); ?>
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
                    Filter
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
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'white','wire:click' => 'resetFilter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'white','wire:click' => 'resetFilter']); ?>
                    Reset
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
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalddfdd8eb2a2b0d47c29a0436357ad57f)): ?>
<?php $attributes = $__attributesOriginalddfdd8eb2a2b0d47c29a0436357ad57f; ?>
<?php unset($__attributesOriginalddfdd8eb2a2b0d47c29a0436357ad57f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalddfdd8eb2a2b0d47c29a0436357ad57f)): ?>
<?php $component = $__componentOriginalddfdd8eb2a2b0d47c29a0436357ad57f; ?>
<?php unset($__componentOriginalddfdd8eb2a2b0d47c29a0436357ad57f); ?>
<?php endif; ?>
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

    
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['padding' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['padding' => 'false']); ?>
        <?php if (isset($component)) { $__componentOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table','data' => ['headers' => ['Tanggal', 'Hari', 'Check-in', 'Check-out', 'Durasi', 'Status', 'Lokasi'],'striped' => 'true','hoverable' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Tanggal', 'Hari', 'Check-in', 'Check-out', 'Durasi', 'Status', 'Lokasi']),'striped' => 'true','hoverable' => 'true']); ?>
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php if (isset($component)) { $__componentOriginalb4e1d3352348902d30955c9827e95353 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4e1d3352348902d30955c9827e95353 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-row','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e($attendance->check_in->format('d/m/Y')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e($attendance->check_in->locale('id')->dayName); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e($attendance->check_in->format('H:i')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e($attendance->check_out ? $attendance->check_out->format('H:i') : '-'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <!--[if BLOCK]><![endif]--><?php if($attendance->check_out): ?>
                            <?php echo e($attendance->check_in->diffInHours($attendance->check_out)); ?>j 
                            <?php echo e($attendance->check_in->diffInMinutes($attendance->check_out) % 60); ?>m
                        <?php else: ?>
                            -
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger'),'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger')),'size' => 'sm']); ?>
                            <?php echo e(ucfirst($attendance->status)); ?>

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
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <!--[if BLOCK]><![endif]--><?php if($attendance->location_lat && $attendance->location_lng): ?>
                            <a href="https://maps.google.com/?q=<?php echo e($attendance->location_lat); ?>,<?php echo e($attendance->location_lng); ?>" 
                               target="_blank" 
                               class="text-primary-600 hover:text-primary-800 text-sm inline-flex items-center">
                                <?php if (isset($component)) { $__componentOriginal56804098dcf376a0e2227cb77b6cd00a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56804098dcf376a0e2227cb77b6cd00a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon','data' => ['name' => 'map-pin','class' => 'w-4 h-4 mr-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'map-pin','class' => 'w-4 h-4 mr-1']); ?>
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
                                Lihat Peta
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb4e1d3352348902d30955c9827e95353)): ?>
<?php $attributes = $__attributesOriginalb4e1d3352348902d30955c9827e95353; ?>
<?php unset($__attributesOriginalb4e1d3352348902d30955c9827e95353); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb4e1d3352348902d30955c9827e95353)): ?>
<?php $component = $__componentOriginalb4e1d3352348902d30955c9827e95353; ?>
<?php unset($__componentOriginalb4e1d3352348902d30955c9827e95353); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php if (isset($component)) { $__componentOriginalb4e1d3352348902d30955c9827e95353 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4e1d3352348902d30955c9827e95353 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-row','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <?php if (isset($component)) { $__componentOriginal026d0ae70983922f13829f6932913f9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal026d0ae70983922f13829f6932913f9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.table-cell','data' => ['class' => 'text-center py-8 text-gray-500','colspan' => '7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.table-cell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-center py-8 text-gray-500','colspan' => '7']); ?>
                        <?php if (isset($component)) { $__componentOriginalfe16eb12133e72aabae529d081318460 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe16eb12133e72aabae529d081318460 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.empty-state','data' => ['icon' => 'document-text','title' => 'Tidak ada data absensi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'document-text','title' => 'Tidak ada data absensi']); ?>
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
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $attributes = $__attributesOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__attributesOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal026d0ae70983922f13829f6932913f9f)): ?>
<?php $component = $__componentOriginal026d0ae70983922f13829f6932913f9f; ?>
<?php unset($__componentOriginal026d0ae70983922f13829f6932913f9f); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb4e1d3352348902d30955c9827e95353)): ?>
<?php $attributes = $__attributesOriginalb4e1d3352348902d30955c9827e95353; ?>
<?php unset($__attributesOriginalb4e1d3352348902d30955c9827e95353); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb4e1d3352348902d30955c9827e95353)): ?>
<?php $component = $__componentOriginalb4e1d3352348902d30955c9827e95353; ?>
<?php unset($__componentOriginalb4e1d3352348902d30955c9827e95353); ?>
<?php endif; ?>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27)): ?>
<?php $attributes = $__attributesOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27; ?>
<?php unset($__attributesOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27)): ?>
<?php $component = $__componentOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27; ?>
<?php unset($__componentOriginal8ed4d2c4c8d055b5cd9b81025b3a3f27); ?>
<?php endif; ?>
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

    
    <?php if (isset($component)) { $__componentOriginal49b03e4fe6969761a6e8370f5d162f5c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49b03e4fe6969761a6e8370f5d162f5c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data.pagination','data' => ['paginator' => $attendances]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data.pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attendances)]); ?>
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
</div>
<?php /**PATH C:\laragon\www\Kopma\resources\views/livewire/attendance/history.blade.php ENDPATH**/ ?>