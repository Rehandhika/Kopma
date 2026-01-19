<div class="space-y-4 sm:space-y-6">
    
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl overflow-hidden shadow-lg">
        <div class="p-4 sm:p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm shrink-0">
                        <!--[if BLOCK]><![endif]--><?php if(auth()->check()): ?>
                            <span class="text-white font-bold text-xl sm:text-2xl"><?php echo e(substr(auth()->user()->name, 0, 1)); ?></span>
                        <?php else: ?>
                            <span class="text-white font-bold text-xl">?</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="min-w-0">
                        <!--[if BLOCK]><![endif]--><?php if(auth()->check()): ?>
                            <h1 class="text-lg sm:text-xl font-bold truncate">Halo, <?php echo e(auth()->user()->name); ?>!</h1>
                            <p class="text-primary-100 text-xs sm:text-sm mt-0.5">
                                <?php echo e(auth()->user()->nim ?? '-'); ?> • 
                                <?php echo e(auth()->user()->roles?->pluck('name')?->join(', ') ?? 'User'); ?>

                            </p>
                        <?php else: ?>
                            <h1 class="text-lg sm:text-xl font-bold">Selamat datang di SIKOPMA</h1>
                            <p class="text-primary-100 text-sm">Silakan login untuk melanjutkan</p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <div class="text-left sm:text-right bg-white/10 rounded-lg px-3 py-2 sm:px-4 sm:py-3">
                    <p class="text-xs text-primary-100"><?php echo e(now()->translatedFormat('l')); ?></p>
                    <p class="text-sm sm:text-base font-semibold"><?php echo e(now()->translatedFormat('d F Y')); ?></p>
                    <p class="text-xl sm:text-2xl font-bold mt-0.5" x-data x-init="
                        setInterval(() => {
                            $el.textContent = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit'})
                        }, 1000)
                    "><?php echo e(now()->format('H:i:s')); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kehadiran</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                        <?php echo e($this->userStats['monthlyAttendance']['present']); ?>/<?php echo e($this->userStats['monthlyAttendance']['total']); ?>

                    </p>
                    <p class="text-xs text-gray-400">bulan ini</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Terlambat</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                        <?php echo e($this->userStats['monthlyAttendance']['late']); ?>

                    </p>
                    <p class="text-xs text-gray-400">kali</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Penalti</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                        <?php echo e($this->userStats['penalties']['count']); ?>

                    </p>
                    <p class="text-xs text-gray-400"><?php echo e($this->userStats['penalties']['points']); ?> poin</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Notifikasi</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                        <?php echo e($this->userStats['notificationCount']); ?>

                    </p>
                    <p class="text-xs text-gray-400">belum dibaca</p>
                </div>
            </div>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($this->isAdmin): ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-sm text-gray-900 dark:text-white">Statistik Admin Hari Ini</h2>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border-l-4 border-emerald-500 pl-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kehadiran</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                        <?php echo e($this->adminStats['todayAttendance']['present']); ?>/<?php echo e($this->adminStats['todayAttendance']['total']); ?>

                    </p>
                </div>
                <div class="border-l-4 border-blue-500 pl-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Penjualan</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(format_currency($this->adminStats['todaySales'])); ?></p>
                    <p class="text-xs text-gray-400"><?php echo e($this->adminStats['todayTransactions']); ?> transaksi</p>
                </div>
                <div class="border-l-4 border-amber-500 pl-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Stok Rendah</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($this->adminStats['lowStockProducts']); ?></p>
                    <p class="text-xs text-gray-400">produk</p>
                </div>
                <div class="border-l-4 border-red-500 pl-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Persetujuan</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($this->adminStats['pendingLeaves'] + $this->adminStats['pendingSwaps']); ?></p>
                    <p class="text-xs text-gray-400"><?php echo e($this->adminStats['pendingLeaves']); ?> cuti, <?php echo e($this->adminStats['pendingSwaps']); ?> tukar</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-sm text-gray-900 dark:text-white">Jadwal Hari Ini</h2>
            </div>
            <div class="p-4">
                <!--[if BLOCK]><![endif]--><?php if($this->userStats['todaySchedule']): ?>
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-blue-900 dark:text-blue-100">Sesi <?php echo e($this->userStats['todaySchedule']->session); ?></h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-0.5">
                                    <?php echo e($this->userStats['todaySchedule']->date->translatedFormat('d F Y')); ?>

                                </p>
                                <a href="<?php echo e(route('admin.attendance.check-in-out')); ?>" 
                                    class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    Check-in Sekarang
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal hari ini</p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-sm text-gray-900 dark:text-white">Notifikasi Terbaru</h2>
                <!--[if BLOCK]><![endif]--><?php if($this->userStats['notifications']->count() > 0): ?>
                    <a href="<?php echo e(route('admin.notifications.index')); ?>" class="text-xs text-primary-600 hover:text-primary-700">Lihat Semua</a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $this->userStats['notifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($notification->title); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2"><?php echo e($notification->message); ?></p>
                                <p class="text-xs text-gray-400 mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi baru</p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($this->userStats['upcomingSchedules']->count() > 0): ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-sm text-gray-900 dark:text-white">Jadwal Mendatang (7 Hari)</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->userStats['upcomingSchedules']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-12 text-center shrink-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($schedule->date->translatedFormat('D')); ?></p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e($schedule->date->format('d')); ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Sesi <?php echo e($schedule->session); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($schedule->date->translatedFormat('F Y')); ?></p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <?php echo e(ucfirst($schedule->status)); ?>

                    </span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>
<?php /**PATH C:\laragon\www\Kopma\resources\views/livewire/dashboard/index.blade.php ENDPATH**/ ?>