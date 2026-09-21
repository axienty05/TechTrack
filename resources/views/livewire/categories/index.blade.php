<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">Master Kategori IT</h1>
            <p class="text-sm opacity-60">Kelola klasifikasi penanganan insiden dan target Service Level Agreement (SLA)</p>
        </div>
        <x-mary-button label="Tambah Kategori" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg" />
    </div>

    <div class="bg-base-100 p-4 rounded-2xl shadow-md border border-base-content/5 flex flex-wrap gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau kode kategori..." icon="o-magnifying-glass" clearable />
        </div>
    </div>

    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Target SLA</th>
                        <th>Warna Label</th>
                        <th>Jumlah Tiket</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr class="hover">
                            <td>
                                <span class="badge badge-neutral font-mono font-bold">{{ $cat->code }}</span>
                            </td>
                            <td>
                                <div class="font-bold text-sm">{{ $cat->name }}</div>
                                <div class="text-xs opacity-50 font-mono">{{ $cat->slug }}</div>
                            </td>
                            <td>
                                <span class="badge badge-outline badge-sm">{{ $cat->default_sla_minutes }} Menit</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full border border-base-content/20 inline-block" style="background-color: {{ $cat->color_hex ?? '#3b82f6' }};"></span>
                                    <span class="text-xs font-mono opacity-70">{{ $cat->color_hex ?? '#3b82f6' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm">{{ $cat->work_logs_count }} tiket</span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $cat->id }})" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-warning" />
                                    </button>
                                    <button wire:click="delete({{ $cat->id }})" wire:confirm="Hapus kategori ini?" class="btn btn-ghost btn-xs btn-square text-error" title="Hapus">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 opacity-50">Tidak ada kategori ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $categories->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <!-- Modal Form Kategori -->
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-md p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <x-mary-icon name="o-tag" class="text-primary" />
            {{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
        </h3>
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Nama Kategori *</label>
                <x-mary-input wire:model="name" placeholder="Contoh: Hardware & Software (PC)" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Kode Singkat *</label>
                    <x-mary-input wire:model="code" placeholder="PC, CCTV, PWR..." />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Target SLA (Menit) *</label>
                    <x-mary-input type="number" wire:model="default_sla_minutes" placeholder="60" />
                </div>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Warna Aksen Badge</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="color_hex" class="w-12 h-10 rounded border border-base-content/20 cursor-pointer p-0.5 bg-transparent" />
                    <x-mary-input wire:model="color_hex" placeholder="#3b82f6" class="flex-1" />
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md">Simpan</button>
            </div>
        </form>
    </x-mary-modal>
</div>