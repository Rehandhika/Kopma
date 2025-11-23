<div>
    <!-- Page Header -->
    <x-layout.page-header title="Manajemen Role">
        <x-slot:actions>
            <x-ui.button wire:click="create" icon="plus">
                Tambah Role
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <!-- Roles Table -->
    <x-ui.card>
        <x-data.table :headers="['Role', 'Anggota', 'Permissions', 'Tipe', 'Aksi']">
            @foreach($roles as $role)
                <x-data.table-row>
                    <x-data.table-cell>
                        <div class="font-medium text-gray-900 capitalize">{{ $role->name }}</div>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <span class="text-gray-700">{{ $role->users_count }} anggota</span>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <div class="flex flex-wrap gap-1">
                            @forelse($role->permissions->take(3) as $permission)
                                <x-ui.badge variant="gray" size="sm">{{ $permission->name }}</x-ui.badge>
                            @empty
                                <span class="text-sm text-gray-400">No permissions</span>
                            @endforelse
                            @if($role->permissions->count() > 3)
                                <x-ui.badge variant="info" size="sm">+{{ $role->permissions->count() - 3 }}</x-ui.badge>
                            @endif
                        </div>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        @if(in_array($role->name, ['super-admin', 'ketua', 'wakil-ketua', 'bph', 'anggota']))
                            <x-ui.badge variant="primary" size="sm">System</x-ui.badge>
                        @else
                            <x-ui.badge variant="secondary" size="sm">Custom</x-ui.badge>
                        @endif
                    </x-data.table-cell>
                    <x-data.table-cell>
                        <div class="flex items-center space-x-2">
                            <x-ui.button wire:click="edit({{ $role->id }})" variant="white" size="sm" icon="pencil">
                                Edit
                            </x-ui.button>
                            @if(!in_array($role->name, ['super-admin', 'ketua', 'wakil-ketua', 'bph', 'anggota']))
                                <x-ui.button 
                                    wire:click="delete({{ $role->id }})" 
                                    wire:confirm="Yakin ingin menghapus role ini?"
                                    variant="danger" 
                                    size="sm" 
                                    icon="trash">
                                    Hapus
                                </x-ui.button>
                            @endif
                        </div>
                    </x-data.table-cell>
                </x-data.table-row>
            @endforeach
        </x-data.table>
    </x-ui.card>

    <!-- Modal -->
    @if($showModal)
        <div x-data="{ show: @entangle('showModal') }">
            <x-ui.modal 
                name="role-modal" 
                :title="$editMode ? 'Edit Role' : 'Tambah Role'" 
                maxWidth="2xl"
                x-show="show"
                style="display: none;">
                
                <form wire:submit="save" class="space-y-4">
                    <x-ui.input 
                        label="Nama Role" 
                        name="name" 
                        wire:model="name"
                        placeholder="contoh: manager, supervisor"
                        required
                        :error="$errors->first('name')"
                        help="Gunakan huruf kecil dan tanpa spasi"
                    />

                    <x-ui.textarea 
                        label="Deskripsi" 
                        name="description" 
                        wire:model="description"
                        rows="2"
                        placeholder="Deskripsi singkat tentang role ini"
                        :error="$errors->first('description')"
                    />

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Permissions</label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto bg-gray-50">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($permissions as $permission)
                                    <x-ui.checkbox 
                                        :name="'permission_' . $permission->id"
                                        :label="$permission->name"
                                        wire:model="selectedPermissions"
                                        value="{{ $permission->name }}"
                                    />
                                @endforeach
                            </div>
                        </div>
                        @error('selectedPermissions')
                            <p class="text-xs text-red-600 flex items-center">
                                <x-ui.icon name="exclamation-circle" class="w-4 h-4 mr-1" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </form>

                <x-slot:footer>
                    <x-ui.button type="button" wire:click="$set('showModal', false)" variant="white">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" wire:click="save">
                        {{ $editMode ? 'Update' : 'Simpan' }}
                    </x-ui.button>
                </x-slot:footer>
            </x-ui.modal>
        </div>
    @endif
</div>
