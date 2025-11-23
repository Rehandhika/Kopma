<div class="space-y-6">
    <!-- Page Header -->
    <x-layout.page-header 
        title="Denda Saya"
        description="Lihat daftar denda yang perlu Anda bayar"
    />

    <!-- Empty State -->
    <x-ui.card>
        <x-layout.empty-state
            icon="currency-dollar"
            title="Tidak ada denda"
            description="Anda tidak memiliki denda saat ini"
        />
    </x-ui.card>
</div>
