<div class="space-y-6">
    <x-layout.page-header
        title="Poin SHU - Monitoring & Pencairan"
        description="Pantau saldo poin, kelola mahasiswa, dan catat pencairan poin"
    />

    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button
                wire:click="setTab('students')"
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'students' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Data Mahasiswa
                </span>
            </button>
            <button
                wire:click="setTab('redemptions')"
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'redemptions' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Pencairan Poin
                </span>
            </button>
        </nav>
    </div>

    {{-- Students Tab --}}
    @if($activeTab === 'students')
        <x-ui.card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari NIM atau nama..."
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-primary-500"
                    >
                </div>
                <div class="flex gap-2">
                    @can('export.shu')
                        <button type="button" wire:click="exportStudentsExcel" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold rounded-xl">
                            Export Excel
                        </button>
                    @endcan
                    @can('manage.shu_students')
                        <button type="button" wire:click="createStudent" class="px-4 py-2.5 bg-primary-600 text-white font-semibold rounded-xl">
                            Tambah Mahasiswa
                        </button>
                    @endcan
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold cursor-pointer" wire:click="sortBy('nim')">NIM</th>
                            <th class="px-4 py-3 text-left font-semibold cursor-pointer" wire:click="sortBy('full_name')">Nama</th>
                            <th class="px-4 py-3 text-right font-semibold cursor-pointer" wire:click="sortBy('points_balance')">Saldo Poin</th>
                            <th class="px-4 py-3 text-left font-semibold cursor-pointer" wire:click="sortBy('created_at')">Dibuat</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($this->students as $student)
                            <tr class="text-gray-900 dark:text-gray-100">
                                <td class="px-4 py-3 font-mono">{{ $student->nim }}</td>
                                <td class="px-4 py-3">{{ $student->full_name }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ number_format($student->points_balance, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $student->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.poin-shu.student', $student) }}" class="px-3 py-1.5 bg-primary-600 text-white rounded-lg font-semibold">
                                            Detail
                                        </a>
                                        @can('manage.shu_students')
                                            <button type="button" wire:click="editStudent({{ $student->id }})" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg">Ubah</button>
                                            <button type="button" wire:click="deleteStudent({{ $student->id }})" wire:confirm="Hapus mahasiswa ini?" class="px-3 py-1.5 bg-red-600 text-white rounded-lg">Hapus</button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->students->links() }}
            </div>
        </x-ui.card>
    @endif

    {{-- Redemptions Tab --}}
    @if($activeTab === 'redemptions')
        {{-- Redemption Form --}}
        @can('redeem.shu')
            <x-ui.card>
                <x-layout.form-section
                    title="Form Pencairan Poin"
                    description="Catat pencairan poin untuk mahasiswa"
                >
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">NIM</label>
                            <input type="text" wire:model.defer="studentNim" inputmode="numeric" maxlength="9" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl" placeholder="NIM mahasiswa">
                            @error('studentNim') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Poin Dicairkan</label>
                            <input type="number" min="1" wire:model.defer="points" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                            @error('points') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nominal (Opsional)</label>
                            <input type="number" min="0" wire:model.defer="cash_amount" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                            @error('cash_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="lg:flex lg:items-end">
                            <button type="button" wire:click="redeem" class="w-full px-4 py-2.5 bg-red-600 text-white font-semibold rounded-xl">
                                Simpan Pencairan
                            </button>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Catatan (Opsional)</label>
                        <input type="text" wire:model.defer="notes" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-layout.form-section>
            </x-ui.card>
        @endcan

        {{-- Redemptions List --}}
        <x-ui.card>
            <x-layout.form-section
                title="Riwayat Pencairan"
                description="Daftar pencairan poin yang telah dicatat"
            >
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 flex-1">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                            <input type="text" wire:model.live.debounce.300ms="redemptionSearch" placeholder="Cari NIM atau nama..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Dari</label>
                            <input type="date" wire:model.live="dateFrom" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sampai</label>
                            <input type="date" wire:model.live="dateTo" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        </div>
                    </div>
                    <div>
                        @can('export.shu')
                            <button type="button" wire:click="exportRedemptionsExcel" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold rounded-xl">
                                Export Excel
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left font-semibold">NIM</th>
                                <th class="px-4 py-3 text-left font-semibold">Nama</th>
                                <th class="px-4 py-3 text-right font-semibold">Poin</th>
                                <th class="px-4 py-3 text-right font-semibold">Nominal</th>
                                <th class="px-4 py-3 text-left font-semibold">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($this->redemptions as $trx)
                                <tr class="text-gray-900 dark:text-gray-100">
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $trx->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 font-mono">{{ $trx->student?->nim }}</td>
                                    <td class="px-4 py-3">{{ $trx->student?->full_name }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-red-700 dark:text-red-400">{{ number_format(abs($trx->points), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $trx->cash_amount ? number_format($trx->cash_amount, 0, ',', '.') : '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $trx->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $this->redemptions->links() }}
                </div>
            </x-layout.form-section>
        </x-ui.card>
    @endif

    {{-- Student Modal --}}
    @if($showStudentModal)
        <div class="fixed inset-0 z-50 flex items-end lg:items-center lg:justify-center p-0 lg:p-4">
            <div wire:click="closeStudentModal" class="absolute inset-0 bg-black/50"></div>

            <div class="relative w-full lg:max-w-md lg:mx-auto bg-white dark:bg-gray-800 rounded-t-2xl lg:rounded-2xl max-h-[85vh] lg:max-h-[80vh] flex flex-col" @click.stop>
                <header class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $editMode ? 'Ubah Mahasiswa' : 'Tambah Mahasiswa' }}</h3>
                    <button type="button" wire:click="closeStudentModal" class="p-1.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </header>

                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    <p class="text-sm text-gray-500">NIM harus 9 digit angka dan unik.</p>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">NIM</label>
                        <input type="text" wire:model.defer="nim" inputmode="numeric" maxlength="9" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                        @error('nim') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model.defer="full_name" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                        @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <footer class="border-t border-gray-200 dark:border-gray-700 p-4 flex items-center justify-end gap-2 bg-gray-50 dark:bg-gray-900/40">
                    <button type="button" wire:click="closeStudentModal" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold rounded-xl">Batal</button>
                    <button type="button" wire:click="saveStudent" class="px-4 py-2.5 bg-primary-600 text-white font-semibold rounded-xl">Simpan</button>
                </footer>
            </div>
        </div>
    @endif
</div>
