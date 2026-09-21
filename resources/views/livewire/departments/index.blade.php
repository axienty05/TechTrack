<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">Master Departemen / Divisi</h1>
            <p class="text-sm opacity-60">Kelola daftar divisi perusahaan, lokasi lantai, dan pemetaan pemohon tiket IT</p>
        </div>
        <x-mary-button label="Tambah Departemen" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg" />
    </div>

    <div class="bg-base-100 p-4 rounded-2xl shadow-md border border-base-content/5 flex flex-wrap gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau kode divisi..." icon="o-magnifying-glass" clearable />
        </div>
    </div>

    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Departemen / Divisi</th>
                        <th>Lokasi / Lantai</th>
                        <th>Riwayat Tiket</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dep)
                        <tr class="hover">
                            <td>
                                <span class="badge badge-primary font-mono font-bold">{{ $dep->code }}</span>
                            </td>
                            <td>
                                <div class="font-bold text-sm">{{ $dep->name }}</div>
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm">{{ $dep->floor_location ?: 'Gedung Utama' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-neutral badge-sm">{{ $dep->work_logs_count }} tiket</span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $dep->id }})" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-warning" />
                                    </button>
                                    <button wire:click="delete({{ $dep->id }})" wire:confirm="Hapus divisi ini?" class="btn btn-ghost btn-xs btn-square text-error" title="Hapus">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 opacity-50">Tidak ada departemen ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $departments->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <!-- Modal Form Departemen -->
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-md p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <x-mary-icon name="o-building-office" class="text-primary" />
            {{ $departmentId ? 'Edit Departemen' : 'Tambah Departemen Baru' }}
        </h3>
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Kode Divisi *</label>
                <x-mary-input wire:model="code" placeholder="Contoh: IT, HRD, FA, EXIM..." />
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Nama Departemen *</label>
                <x-mary-input wire:model="name" placeholder="Contoh: Finance & Accounting" />
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Lokasi / Lantai</label>
                <x-mary-input wire:model="floor_location" placeholder="Contoh: Lantai 2, Gedung Barat" />
            </div>
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md">Simpan</button>
            </div>
        </form>
    </x-mary-modal>
</div>