<div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
    
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Jadwal Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Lihat jadwal shift Anda</p>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between gap-2">
            <button wire:click="previousWeek" 
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="text-center flex-1 min-w-0">
                <p class="text-sm sm:text-base font-semibold text-gray-900 truncate">
                    <?php echo e($currentWeekStart->format('d M')); ?> - <?php echo e($currentWeekEnd->format('d M Y')); ?>

                </p>
                <!--[if BLOCK]><![endif]--><?php if($weekOffset === 0): ?>
                    <span class="inline-flex mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                        Minggu Ini
                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <button wire:click="nextWeek" 
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!--[if BLOCK]><![endif]--><?php if($weekOffset !== 0): ?>
            <div class="mt-3 pt-3 border-t border-gray-100 text-center">
                <button wire:click="currentWeek" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    ← Kembali ke Minggu Ini
                </button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($upcomingSchedules->count() > 0): ?>
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 mb-6 text-white">
        <h3 class="text-sm font-medium opacity-90 mb-3">Jadwal Terdekat</h3>
        <div class="space-y-2">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $upcomingSchedules->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between bg-white/10 backdrop-blur rounded-lg p-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold"><?php echo e($schedule->date->format('d')); ?></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate"><?php echo e($schedule->date->locale('id')->format('l')); ?></p>
                        <p class="text-xs opacity-80">
                            Sesi <?php echo e($schedule->session); ?> • 
                            <?php echo e(\Carbon\Carbon::parse($schedule->time_start)->format('H:i')); ?>

                        </p>
                    </div>
                </div>
                <span class="text-xs opacity-80 flex-shrink-0 ml-2"><?php echo e($schedule->date->diffForHumans()); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Jadwal Mingguan</h2>
        </div>

        <div class="divide-y divide-gray-100">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $dateKey = $day['date']->format('Y-m-d');
                    $assignments = $mySchedules[$dateKey] ?? collect();
                ?>
                
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'p-4 transition-colors',
                    'bg-blue-50/50' => $day['isToday'],
                    'opacity-60' => $day['isPast'] && !$day['isToday'],
                ]); ?>">
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold',
                                'bg-blue-600 text-white' => $day['isToday'],
                                'bg-gray-100 text-gray-600' => !$day['isToday'],
                            ]); ?>">
                                <?php echo e($day['date']->format('d')); ?>

                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($day['dayName']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($day['date']->format('M Y')); ?></p>
                            </div>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php if($day['isToday']): ?>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                Hari Ini
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <!--[if BLOCK]><![endif]--><?php if($assignments->count() > 0): ?>
                        <div class="space-y-2 ml-12">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'flex items-center justify-between p-3 rounded-lg border-l-4',
                                    'bg-green-50 border-green-500' => $assignment->session == 1,
                                    'bg-amber-50 border-amber-500' => $assignment->session == 2,
                                    'bg-purple-50 border-purple-500' => $assignment->session == 3,
                                ]); ?>">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Sesi <?php echo e($assignment->session); ?></p>
                                        <!--[if BLOCK]><![endif]--><?php if($assignment->time_start && $assignment->time_end): ?>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <?php echo e(\Carbon\Carbon::parse($assignment->time_start)->format('H:i')); ?> - 
                                                <?php echo e(\Carbon\Carbon::parse($assignment->time_end)->format('H:i')); ?>

                                            </p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php if(!$day['isPast']): ?>
                                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            'bg-green-100 text-green-700' => $assignment->session == 1,
                                            'bg-amber-100 text-amber-700' => $assignment->session == 2,
                                            'bg-purple-100 text-purple-700' => $assignment->session == 3,
                                        ]); ?>">
                                            <!--[if BLOCK]><![endif]--><?php if($assignment->session == 1): ?> Pagi
                                            <?php elseif($assignment->session == 2): ?> Siang
                                            <?php else: ?> Sore
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div class="ml-12 py-3 text-center">
                            <p class="text-sm text-gray-400">Tidak ada jadwal</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    
    <div class="grid grid-cols-3 gap-3 mt-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">
                <?php echo e($mySchedules->flatten()->count()); ?>

            </p>
            <p class="text-xs text-gray-500 mt-1">Shift Minggu Ini</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">
                <?php echo e($upcomingSchedules->count()); ?>

            </p>
            <p class="text-xs text-gray-500 mt-1">Mendatang</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">
                <?php echo e($mySchedules->flatten()->count() * 3); ?>

            </p>
            <p class="text-xs text-gray-500 mt-1">Jam</p>
        </div>
    </div>

    
    <div wire:loading.flex class="fixed inset-0 bg-black/30 items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 shadow-xl flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Memuat...</span>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Kopma\resources\views/livewire/schedule/my-schedule.blade.php ENDPATH**/ ?>