<div class="space-y-6">
    <!-- Page Header -->
    <x-layout.page-header 
        title="Kelola Denda"
        description="Kelola dan verifikasi pembayaran denda karyawan"
    />

    <!-- Info Alert -->
    <x-ui.alert variant="info" icon>
        <div class="font-medium">Informasi</div>
        <div class="mt-1 text-sm">
            Halaman ini digunakan untuk mengelola pembayaran denda karyawan. Verifikasi pembayaran yang masuk dan update status denda.
        </div>
    </x-ui.alert>

    <!-- Empty State -->
    <x-ui.card>
        <x-layout.empty-state
            icon="clipboard-check"
            title="Tidak ada denda yang perlu dikelola"
            description="Semua denda sudah terbayar atau tidak ada denda aktif saat ini"
        />
    </x-ui.card>
</div>
