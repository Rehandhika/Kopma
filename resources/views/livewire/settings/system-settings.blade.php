<div class="space-y-6">
    <x-layout.page-header 
        title="Pengaturan Sistem"
        description="Kelola pengaturan sistem dan konfigurasi aplikasi"
    />

    <x-ui.card>
        <form wire:submit="save" class="space-y-6">
            <x-layout.form-section 
                title="Pengaturan Waktu"
                description="Konfigurasi zona waktu dan format tanggal"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-ui.select
                            label="Zona Waktu"
                            name="timezone"
                            wire:model="timezone"
                            :options="[
                                'Asia/Jakarta' => 'WIB (Jakarta)',
                                'Asia/Makassar' => 'WITA (Makassar)',
                                'Asia/Jayapura' => 'WIT (Jayapura)',
                            ]"
                        />
                    </div>

                    <div>
                        <x-ui.select
                            label="Format Tanggal"
                            name="date_format"
                            wire:model="date_format"
                            :options="[
                                'd/m/Y' => 'DD/MM/YYYY',
                                'm/d/Y' => 'MM/DD/YYYY',
                                'Y-m-d' => 'YYYY-MM-DD',
                            ]"
                        />
                    </div>
                </div>
            </x-layout.form-section>

            <x-layout.form-section 
                title="Pengaturan Keamanan"
                description="Konfigurasi keamanan dan autentikasi"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-ui.input
                            label="Durasi Sesi (menit)"
                            name="session_lifetime"
                            type="number"
                            wire:model="session_lifetime"
                            placeholder="120"
                            help="Durasi sesi pengguna sebelum logout otomatis"
                        />
                    </div>

                    <div>
                        <x-ui.input
                            label="Maksimal Percobaan Login"
                            name="max_login_attempts"
                            type="number"
                            wire:model="max_login_attempts"
                            placeholder="5"
                            help="Jumlah maksimal percobaan login sebelum akun dikunci"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <x-ui.checkbox
                            label="Aktifkan Verifikasi Email"
                            name="email_verification"
                            wire:model="email_verification"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <x-ui.checkbox
                            label="Aktifkan Two-Factor Authentication"
                            name="two_factor_auth"
                            wire:model="two_factor_auth"
                        />
                    </div>
                </div>
            </x-layout.form-section>

            <x-layout.form-section 
                title="Pengaturan Notifikasi"
                description="Konfigurasi sistem notifikasi"
            >
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <x-ui.checkbox
                            label="Notifikasi Email"
                            name="email_notifications"
                            wire:model="email_notifications"
                        />
                    </div>

                    <div>
                        <x-ui.checkbox
                            label="Notifikasi Browser"
                            name="browser_notifications"
                            wire:model="browser_notifications"
                        />
                    </div>

                    <div>
                        <x-ui.checkbox
                            label="Notifikasi SMS"
                            name="sms_notifications"
                            wire:model="sms_notifications"
                        />
                    </div>
                </div>
            </x-layout.form-section>

            <x-layout.form-section 
                title="Pengaturan Maintenance"
                description="Mode pemeliharaan dan backup"
            >
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <x-ui.checkbox
                            label="Mode Maintenance"
                            name="maintenance_mode"
                            wire:model="maintenance_mode"
                        />
                        <p class="mt-2 text-sm text-gray-500">
                            Aktifkan mode maintenance untuk mencegah akses pengguna saat pemeliharaan sistem
                        </p>
                    </div>

                    <div>
                        <x-ui.checkbox
                            label="Backup Otomatis"
                            name="auto_backup"
                            wire:model="auto_backup"
                        />
                    </div>

                    <div>
                        <x-ui.select
                            label="Frekuensi Backup"
                            name="backup_frequency"
                            wire:model="backup_frequency"
                            :options="[
                                'daily' => 'Harian',
                                'weekly' => 'Mingguan',
                                'monthly' => 'Bulanan',
                            ]"
                        />
                    </div>
                </div>
            </x-layout.form-section>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <x-ui.button 
                    type="submit" 
                    variant="primary"
                    icon="check"
                >
                    Simpan Pengaturan
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
