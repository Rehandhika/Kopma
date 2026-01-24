<div class="space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengajuan Perubahan Jadwal</h1>
            <p class="text-sm text-gray-500">Ajukan pindah atau batalkan jadwal shift Anda</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Perubahan
        </button>
    </div>

    
    <div class="border-b border-gray-200">
        <nav class="flex gap-8">
            <button wire:click="setTab('my-requests')" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['pb-3 text-sm font-medium border-b-2 -mb-px', 'border-indigo-500 text-indigo-600' => $activeTab === 'my-requests', 'border-transparent text-gray-500 hover:text-gray-700' => $activeTab !== 'my-requests']); ?>">
                Pengajuan Saya
            </button>
            <!--[if BLOCK]><![endif]--><?php if($isAdmin): ?>
            <button wire:click="setTab('admin')" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['pb-3 text-sm font-medium border-b-2 -mb-px', 'border-indigo-500 text-indigo-600' => $activeTab === 'admin', 'border-transparent text-gray-500 hover:text-gray-700' => $activeTab !== 'admin']); ?>">
                Persetujuan
                <!--[if BLOCK]><![endif]--><?php if($stats['pending'] > 0): ?>
                <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full"><?php echo e($stats['pending']); ?></span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </button>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </nav>
    </div>

    
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-lg border p-4">
            <p class="text-2xl font-bold text-yellow-600"><?php echo e($stats['pending']); ?></p>
            <p class="text-sm text-gray-500">Menunggu</p>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <p class="text-2xl font-bold text-green-600"><?php echo e($stats['approved']); ?></p>
            <p class="text-sm text-gray-500">Disetujui</p>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <p class="text-2xl font-bold text-red-600"><?php echo e($stats['rejected']); ?></p>
            <p class="text-sm text-gray-500">Ditolak</p>
        </div>
    </div>

    
    <select wire:model.live="statusFilter" class="text-sm border-gray-300 rounded-lg">
        <option value="all">Semua Status</option>
        <option value="pending">Menunggu</option>
        <option value="approved">Disetujui</option>
        <option value="rejected">Ditolak</option>
        <option value="cancelled">Dibatalkan</option>
    </select>

    
    <div class="bg-white rounded-lg border divide-y">
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div wire:key="req-<?php echo e($req->id); ?>" wire:click="viewRequest(<?php echo e($req->id); ?>)" class="p-4 hover:bg-gray-50 cursor-pointer flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <!--[if BLOCK]><![endif]--><?php if($activeTab === 'admin'): ?>
                    <span class="font-medium text-gray-900"><?php echo e($req->user->name ?? '-'); ?></span>
                    <span class="text-gray-300">•</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['px-2 py-0.5 text-xs rounded-full', 'bg-blue-100 text-blue-700' => $req->change_type === 'reschedule', 'bg-orange-100 text-orange-700' => $req->change_type === 'cancel']); ?>">
                        <?php echo e($req->getChangeTypeLabel()); ?>

                    </span>
                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['px-2 py-0.5 text-xs rounded-full', 'bg-yellow-100 text-yellow-700' => $req->status === 'pending', 'bg-green-100 text-green-700' => $req->status === 'approved', 'bg-red-100 text-red-700' => $req->status === 'rejected', 'bg-gray-100 text-gray-700' => $req->status === 'cancelled']); ?>">
                        <?php echo e($req->getStatusLabel()); ?>

                    </span>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($req->originalAssignment): ?>
                <p class="text-sm text-gray-600">
                    Jadwal: <?php echo e(\Carbon\Carbon::parse($req->originalAssignment->date)->format('d M Y')); ?> 
                    • Sesi <?php echo e($req->originalAssignment->session); ?> (<?php echo e($req->originalAssignment->time_start); ?>-<?php echo e($req->originalAssignment->time_end); ?>)
                </p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if($req->change_type === 'reschedule' && $req->requested_date): ?>
                <p class="text-sm text-indigo-600">
                    → Pindah ke: <?php echo e($req->requested_date->format('d M Y')); ?> • <?php echo e($req->getSessionLabel()); ?>

                </p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <p class="text-sm text-gray-400 truncate mt-1"><?php echo e($req->reason); ?></p>
            </div>
            <div class="flex items-center gap-2" wire:click.stop>
                <!--[if BLOCK]><![endif]--><?php if($activeTab === 'admin' && $req->status === 'pending'): ?>
                <button wire:click="openReview(<?php echo e($req->id); ?>, 'approved')" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Setujui">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
                <button wire:click="openReview(<?php echo e($req->id); ?>, 'rejected')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Tolak">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="p-12 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p>Belum ada pengajuan</p>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <?php echo e($requests->links()); ?>


    
    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        <div class="fixed inset-0 bg-black/50" wire:click="closeForm"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md" @click.stop>
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Ajukan Perubahan Jadwal</h3>
                    
                    <form wire:submit="submitForm" class="space-y-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jadwal</label>
                            <select wire:model="selectedAssignment" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">-- Pilih jadwal --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $myAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($asg->id); ?>">
                                    <?php echo e(\Carbon\Carbon::parse($asg->date)->format('d M Y')); ?> - Sesi <?php echo e($asg->session); ?> (<?php echo e($asg->time_start); ?>-<?php echo e($asg->time_end); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedAssignment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Perubahan</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" wire:click="$set('changeType', 'reschedule')" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['p-3 border rounded-lg text-sm font-medium transition', 'border-indigo-500 bg-indigo-50 text-indigo-700' => $changeType === 'reschedule', 'border-gray-200 text-gray-600 hover:border-gray-300' => $changeType !== 'reschedule']); ?>">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    Pindah Jadwal
                                </button>
                                <button type="button" wire:click="$set('changeType', 'cancel')" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['p-3 border rounded-lg text-sm font-medium transition', 'border-orange-500 bg-orange-50 text-orange-700' => $changeType === 'cancel', 'border-gray-200 text-gray-600 hover:border-gray-300' => $changeType !== 'cancel']); ?>">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Batalkan Jadwal
                                </button>
                            </div>
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($changeType === 'reschedule'): ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tujuan</label>
                                <input type="date" wire:model="requestedDate" min="<?php echo e(now()->format('Y-m-d')); ?>" class="w-full border-gray-300 rounded-lg text-sm">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['requestedDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sesi Tujuan</label>
                                <select wire:model="requestedSession" class="w-full border-gray-300 rounded-lg text-sm">
                                    <option value="0">-- Pilih sesi --</option>
                                    <option value="1">Sesi 1 (07:30-10:00)</option>
                                    <option value="2">Sesi 2 (10:20-12:50)</option>
                                    <option value="3">Sesi 3 (13:30-16:00)</option>
                                </select>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['requestedSession'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                            <textarea wire:model="reason" rows="3" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Jelaskan alasan perubahan jadwal..."></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="closeForm" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitForm">Kirim Pengajuan</span>
                                <span wire:loading wire:target="submitForm">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($viewingRequest): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50" wire:click="closeView"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Detail Pengajuan</h3>
                        <button wire:click="closeView" class="p-1 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        
                        <div class="flex gap-2">
                            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['px-3 py-1 text-sm rounded-full', 'bg-yellow-100 text-yellow-700' => $viewingRequest->status === 'pending', 'bg-green-100 text-green-700' => $viewingRequest->status === 'approved', 'bg-red-100 text-red-700' => $viewingRequest->status === 'rejected', 'bg-gray-100 text-gray-700' => $viewingRequest->status === 'cancelled']); ?>">
                                <?php echo e($viewingRequest->getStatusLabel()); ?>

                            </span>
                            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['px-3 py-1 text-sm rounded-full', 'bg-blue-100 text-blue-700' => $viewingRequest->change_type === 'reschedule', 'bg-orange-100 text-orange-700' => $viewingRequest->change_type === 'cancel']); ?>">
                                <?php echo e($viewingRequest->getChangeTypeLabel()); ?>

                            </span>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($activeTab === 'admin'): ?>
                        <div>
                            <p class="text-xs text-gray-500">Pemohon</p>
                            <p class="font-medium"><?php echo e($viewingRequest->user->name ?? '-'); ?></p>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Jadwal Asal</p>
                            <!--[if BLOCK]><![endif]--><?php if($viewingRequest->originalAssignment): ?>
                            <p class="font-medium"><?php echo e(\Carbon\Carbon::parse($viewingRequest->originalAssignment->date)->format('d M Y')); ?></p>
                            <p class="text-sm text-gray-600">Sesi <?php echo e($viewingRequest->originalAssignment->session); ?> (<?php echo e($viewingRequest->originalAssignment->time_start); ?>-<?php echo e($viewingRequest->originalAssignment->time_end); ?>)</p>
                            <?php else: ?>
                            <p class="text-gray-400">Jadwal tidak ditemukan</p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($viewingRequest->change_type === 'reschedule' && $viewingRequest->requested_date): ?>
                        <div class="p-3 bg-indigo-50 rounded-lg">
                            <p class="text-xs text-indigo-600 mb-1">Jadwal Tujuan</p>
                            <p class="font-medium text-indigo-700"><?php echo e($viewingRequest->requested_date->format('d M Y')); ?></p>
                            <p class="text-sm text-indigo-600"><?php echo e($viewingRequest->getSessionLabel()); ?></p>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div>
                            <p class="text-xs text-gray-500">Alasan</p>
                            <p class="text-gray-700"><?php echo e($viewingRequest->reason); ?></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Diajukan</p>
                            <p class="text-gray-700"><?php echo e($viewingRequest->created_at->format('d M Y H:i')); ?></p>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($viewingRequest->admin_responded_at): ?>
                        <div class="p-3 bg-gray-50 rounded-lg text-sm">
                            <p class="text-gray-500">Ditinjau oleh <?php echo e($viewingRequest->adminResponder?->name ?? '-'); ?> • <?php echo e($viewingRequest->admin_responded_at->format('d M Y H:i')); ?></p>
                            <!--[if BLOCK]><![endif]--><?php if($viewingRequest->admin_response): ?>
                            <p class="text-gray-700 mt-1">"<?php echo e($viewingRequest->admin_response); ?>"</p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <div class="mt-6 flex gap-3">
                        <!--[if BLOCK]><![endif]--><?php if($activeTab === 'admin' && $viewingRequest->status === 'pending'): ?>
                            <button wire:click="openReview(<?php echo e($viewingRequest->id); ?>, 'rejected')" class="flex-1 px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50">Tolak</button>
                            <button wire:click="openReview(<?php echo e($viewingRequest->id); ?>, 'approved')" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Setujui</button>
                        <?php elseif($activeTab === 'my-requests' && $viewingRequest->status === 'pending'): ?>
                            <button wire:click="closeView" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Tutup</button>
                            <button wire:click="cancelRequest(<?php echo e($viewingRequest->id); ?>)" wire:confirm="Yakin ingin membatalkan pengajuan ini?" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">Batalkan</button>
                        <?php else: ?>
                            <button wire:click="closeView" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Tutup</button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($reviewingId): ?>
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-black/50" wire:click="closeReview"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <?php echo e($reviewAction === 'approved' ? 'Setujui Pengajuan' : 'Tolak Pengajuan'); ?>

                    </h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                        <textarea wire:model="reviewNotes" rows="3" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Tambahkan catatan..."></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="closeReview" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                        <button wire:click="submitReview" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['flex-1 px-4 py-2 rounded-lg text-sm font-medium text-white', 'bg-green-600 hover:bg-green-700' => $reviewAction === 'approved', 'bg-red-600 hover:bg-red-700' => $reviewAction === 'rejected']); ?>">
                            <?php echo e($reviewAction === 'approved' ? 'Ya, Setujui' : 'Ya, Tolak'); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\laragon\www\Kopma\resources\views/livewire/schedule/schedule-change-manager.blade.php ENDPATH**/ ?>