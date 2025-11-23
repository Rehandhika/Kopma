<div class="space-y-6">
    <x-layout.page-header 
        title="Pengaturan Umum"
        description="Kelola pengaturan umum aplikasi SIKOPMA"
    />

    <x-ui.card>
        <form wire:submit="save" class="space-y-6">
            <x-layout.form-section 
                title="Informasi Aplikasi"
                description="Pengaturan dasar informasi aplikasi"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <x-ui.input
                            label="Nama Aplikasi"
                            name="app_name"
                            type="text"
                            wire:model="app_name"
                            :required="true"
                            :error="$errors->first('app_name')"
                            placeholder="Masukkan nama aplikasi"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <x-ui.textarea
                            label="Deskripsi"
                            name="app_description"
                            wire:model="app_description"
                            :rows="3"
                            placeholder="Masukkan deskripsi aplikasi"
                        />
                    </div>
                </div>
            </x-layout.form-section>

            <x-layout.form-section 
                title="Informasi Kontak"
                description="Pengaturan informasi kontak organisasi"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-ui.input
                            label="Email Kontak"
                            name="contact_email"
                            type="email"
                            wire:model="contact_email"
                            :error="$errors->first('contact_email')"
                            placeholder="email@example.com"
                        />
                    </div>

                    <div>
                        <x-ui.input
                            label="Telepon Kontak"
                            name="contact_phone"
                            type="tel"
                            wire:model="contact_phone"
                            placeholder="+62 xxx xxxx xxxx"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <x-ui.textarea
                            label="Alamat"
                            name="address"
                            wire:model="address"
                            :rows="3"
                            placeholder="Masukkan alamat lengkap"
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
