<div class="space-y-6">
    <x-layout.page-header 
        title="Kelola Banner & Berita"
        description="Kelola banner promosi dan berita yang ditampilkan di halaman publik"
    />

    {{-- Tab Navigation --}}
    <div class="bg-white border-b border-gray-200 rounded-t-lg">
        <nav class="flex -mb-px" aria-label="Tabs">
            <button 
                wire:click="switchTab('banner')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'banner' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Banner
                </div>
            </button>
            <button 
                wire:click="switchTab('news')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'news' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    Berita
                </div>
            </button>
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="mt-6">
        @if($activeTab === 'banner')
            @include('livewire.admin.partials.banner-tab')
        @else
            @include('livewire.admin.partials.news-tab')
        @endif
    </div>
</div>
