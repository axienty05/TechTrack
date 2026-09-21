<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">Manajemen Pengguna (IT Staff)</h1>
            <p class="text-sm opacity-60">Kelola akun teknisi, foto profil, hak akses (role), dan kredensial sistem</p>
        </div>
        <x-mary-button label="Tambah Pengguna" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg" />
    </div>

    <div class="bg-base-100 p-4 rounded-2xl shadow-md border border-base-content/5 flex flex-wrap gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari nama, username, email..." icon="o-magnifying-glass" clearable />
        </div>
    </div>

    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>Role / Hak Akses</th>
                        <th>No. Telepon</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr class="hover">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if($u->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($u->avatar))
                                            <div class="rounded-full overflow-hidden shadow-inner border border-base-content/10" style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;">
                                                <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->name }}" class="w-full h-full object-cover" style="width: 40px; height: 40px; object-fit: cover;" />
                                            </div>
                                        @else
                                            <div class="bg-neutral text-neutral-content rounded-full flex items-center justify-center font-bold text-xs leading-none shadow-inner select-none" style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;">
                                                <span>{{ strtoupper(substr($u->name, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm">{{ $u->name }}</div>
                                        <div class="text-xs font-mono opacity-60">@ {{ $u->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-xs font-mono">{{ $u->email }}</span>
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge badge-primary badge-sm uppercase font-bold text-[10px]">ADMIN</span>
                                @elseif($u->role === 'it_lead')
                                    <span class="badge badge-secondary badge-sm uppercase font-bold text-[10px]">LEAD</span>
                                @else
                                    <span class="badge badge-ghost badge-sm uppercase font-bold text-[10px]">SUPPORT</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-xs opacity-70">{{ $u->phone_number ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-sm {{ $u->is_active ? 'badge-success' : 'badge-ghost opacity-50' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $u->id }})" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-warning" />
                                    </button>
                                    @if($u->id !== auth()->id())
                                        <button wire:click="delete({{ $u->id }})" wire:confirm="Hapus pengguna ini?" class="btn btn-ghost btn-xs btn-square text-error" title="Hapus">
                                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 opacity-50">Tidak ada pengguna ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $users->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <!-- Modal Form User -->
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-md p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <x-mary-icon name="o-user" class="text-primary" />
            {{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
        </h3>
        <form wire:submit="save" class="space-y-4">
            <!-- Foto Profil Input & Preview -->
            <div class="flex items-center gap-4 p-3 bg-base-200/60 rounded-xl border border-base-content/10">
                <div class="flex-shrink-0">
                    @if($avatar)
                        <div class="rounded-full overflow-hidden shadow border-2 border-primary bg-base-200" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px;">
                            <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover" style="width: 64px; height: 64px; object-fit: cover;" />
                        </div>
                    @elseif($currentAvatar)
                        <div class="rounded-full overflow-hidden shadow border-2 border-primary bg-base-200" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px;">
                            <img src="{{ $currentAvatar }}" alt="Current" class="w-full h-full object-cover" style="width: 64px; height: 64px; object-fit: cover;" />
                        </div>
                    @else
                        <div class="bg-neutral text-neutral-content rounded-full flex items-center justify-center font-bold text-lg shadow-inner select-none" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px;">
                            <span>{{ strtoupper(substr($name ?: 'U', 0, 2)) }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 space-y-1.5">
                    <label class="font-bold text-xs uppercase opacity-70 block">Foto Profil (Avatar)</label>
                    <input type="file" wire:model="avatar" accept="image/*" class="file-input file-input-bordered file-input-xs w-full" />
                    @if($currentAvatar)
                        <button type="button" wire:click="deleteAvatar" wire:confirm="Hapus foto profil ini?" class="text-xs text-error hover:underline flex items-center gap-1">
                            <x-mary-icon name="o-trash" class="w-3 h-3" /> Hapus Foto Saat Ini
                        </button>
                    @endif
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Nama Lengkap *</label>
                <x-mary-input wire:model="name" placeholder="Nama Lengkap Teknisi" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Username *</label>
                    <x-mary-input wire:model="username" placeholder="username" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Hak Akses (Role) *</label>
                    <select wire:model="role" class="select select-bordered w-full">
                        <option value="admin">Administrator</option>
                        <option value="it_lead">IT Lead</option>
                        <option value="it_support">IT Support</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Email *</label>
                <x-mary-input type="email" wire:model="email" placeholder="nama@techtrack.local" />
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Nomor Telepon / WA</label>
                <x-mary-input wire:model="phone_number" placeholder="08xxxxxxxxxx" />
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">
                    Password {{ $userId ? '(Kosongkan jika tidak ingin diubah)' : '*' }}
                </label>
                <x-mary-input type="password" wire:model="password" placeholder="Minimal 6 karakter" />
            </div>
            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" wire:model="is_active" class="checkbox checkbox-primary checkbox-sm" />
                    <span class="label-text font-medium text-sm">Akun Aktif</span>
                </label>
            </div>
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md" wire:loading.attr="disabled">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    Simpan
                </button>
            </div>
        </form>
    </x-mary-modal>
</div>