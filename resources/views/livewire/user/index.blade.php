<div class="space-y-6">
    <!-- Header -->
    <x-layout.page-header title="Manajemen Anggota">
        <x-slot:actions>
            <x-ui.button wire:click="create" icon="plus">
                Tambah Anggota
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <!-- Stats -->
    <x-layout.grid cols="3">
        <x-layout.stat-card 
            label="Total Anggota" 
            :value="$stats['total']" 
            icon="users"
            icon-color="bg-primary-100"
            icon-text-color="text-primary-600"
        />
        
        <x-layout.stat-card 
            label="Aktif" 
            :value="$stats['active']" 
            icon="check-circle"
            icon-color="bg-success-50"
            icon-text-color="text-success-600"
        />
        
        <x-layout.stat-card 
            label="Tidak Aktif" 
            :value="$stats['inactive']" 
            icon="x-circle"
            icon-color="bg-danger-50"
            icon-text-color="text-danger-600"
        />
    </x-layout.grid>

    <!-- Filters -->
    <x-ui.card>
        <x-layout.grid cols="3">
            <x-ui.input 
                type="text" 
                wire:model.live="search" 
                placeholder="Cari nama, NIM, atau email..." 
                icon="magnifying-glass"
            />
            
            <x-ui.select wire:model.live="roleFilter">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </x-ui.select>
            
            <x-ui.select wire:model.live="statusFilter">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
            </x-ui.select>
        </x-layout.grid>
    </x-ui.card>

    <!-- Table -->
    <x-ui.card :padding="false">
        <x-data.table :headers="['NIM', 'Nama', 'Email', 'Role', 'Status', 'Aksi']">
            @forelse($users as $user)
                <x-data.table-row>
                    <x-data.table-cell>
                        <span class="font-medium text-gray-900">{{ $user->nim }}</span>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <div class="flex items-center space-x-3">
                            <x-ui.avatar :name="$user->name" size="md" />
                            <div>
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                @if($user->phone)
                                    <div class="text-sm text-gray-500">{{ $user->phone }}</div>
                                @endif
                            </div>
                        </div>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        {{ $user->email }}
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <x-ui.badge 
                                    :variant="$role->name === 'super-admin' ? 'danger' : ($role->name === 'ketua' ? 'primary' : 'secondary')"
                                    size="sm"
                                >
                                    {{ ucfirst($role->name) }}
                                </x-ui.badge>
                            @endforeach
                        </div>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <button wire:click="toggleStatus({{ $user->id }})" class="inline-block">
                            <x-ui.badge 
                                :variant="$user->status === 'active' ? 'success' : 'gray'"
                                size="sm"
                                class="cursor-pointer hover:opacity-75"
                            >
                                {{ $user->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </x-ui.badge>
                        </button>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <div class="flex items-center space-x-2">
                            <button 
                                wire:click="edit({{ $user->id }})" 
                                class="text-primary-600 hover:text-primary-900 transition-colors"
                                title="Edit"
                            >
                                <x-ui.icon name="pencil" class="w-5 h-5" />
                            </button>
                            @if(!$user->hasRole('super-admin') && $user->id !== auth()->id())
                                <button 
                                    wire:click="delete({{ $user->id }})" 
                                    wire:confirm="Yakin ingin menghapus anggota ini?"
                                    class="text-danger-600 hover:text-danger-900 transition-colors"
                                    title="Hapus"
                                >
                                    <x-ui.icon name="trash" class="w-5 h-5" />
                                </button>
                            @endif
                        </div>
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell colspan="6">
                        <x-layout.empty-state 
                            icon="users"
                            title="Tidak ada data anggota"
                            description="Belum ada anggota yang terdaftar atau sesuai dengan filter yang dipilih."
                        />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>
    </x-ui.card>

    <x-data.pagination :paginator="$users" />

    <!-- Modal -->
    <x-ui.modal 
        name="user-form" 
        :title="$editMode ? 'Edit Anggota' : 'Tambah Anggota'"
        max-width="2xl"
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        @close-modal-user-form.window="$wire.set('showModal', false)"
    >
        <form wire:submit="save" class="space-y-4">
            <x-layout.grid cols="2">
                <x-ui.input 
                    label="NIM" 
                    name="nim"
                    type="text" 
                    wire:model="nim" 
                    required
                    :error="$errors->first('nim')"
                />

                <x-ui.input 
                    label="Nama Lengkap" 
                    name="name"
                    type="text" 
                    wire:model="name" 
                    required
                    :error="$errors->first('name')"
                />

                <x-ui.input 
                    label="Email" 
                    name="email"
                    type="email" 
                    wire:model="email" 
                    required
                    :error="$errors->first('email')"
                />

                <x-ui.input 
                    label="No. Telepon" 
                    name="phone"
                    type="text" 
                    wire:model="phone"
                    :error="$errors->first('phone')"
                />

                <div class="md:col-span-2">
                    <x-ui.textarea 
                        label="Alamat" 
                        name="address"
                        wire:model="address" 
                        rows="2"
                        :error="$errors->first('address')"
                    />
                </div>

                <x-ui.input 
                    :label="'Password ' . ($editMode ? '(Kosongkan jika tidak diubah)' : '')" 
                    name="password"
                    type="password" 
                    wire:model="password" 
                    :required="!$editMode"
                    :error="$errors->first('password')"
                />

                <x-ui.input 
                    label="Konfirmasi Password" 
                    name="password_confirmation"
                    type="password" 
                    wire:model="password_confirmation"
                />

                <x-ui.select 
                    label="Status" 
                    name="status"
                    wire:model="status" 
                    required
                    :error="$errors->first('status')"
                >
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </x-ui.select>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Role <span class="text-danger-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($roles as $role)
                            <x-ui.checkbox 
                                :label="ucfirst($role->name)"
                                name="selectedRoles[]"
                                :value="$role->name"
                                wire:model="selectedRoles"
                            />
                        @endforeach
                    </div>
                    @error('selectedRoles')
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-layout.grid>
        </form>

        <x-slot:footer>
            <x-ui.button 
                type="button" 
                variant="white" 
                wire:click="$set('showModal', false)"
            >
                Batal
            </x-ui.button>
            <x-ui.button 
                type="submit" 
                wire:click="save"
            >
                {{ $editMode ? 'Update' : 'Simpan' }}
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
