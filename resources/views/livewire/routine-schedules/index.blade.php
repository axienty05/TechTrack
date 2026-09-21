<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">Jadwal Pemeliharaan Rutin</h1>
            <p class="text-sm opacity-60">Daftar agenda pemeliharaan preventif (maintenance berkala) infrastruktur IT</p>
        </div>
        <x-mary-button label="Tambah Jadwal Rutin" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg" />
    </div>

    <div class="bg-base-100 p-4 rounded-2xl shadow-md border border-base-content/5 flex flex-wrap gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari judul jadwal..." icon="o-magnifying-glass" clearable />
        </div>
    </div>

    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th>Nama Agenda / Tugas</th>
                        <th>Kategori</th>
                        <th>Frekuensi</th>
                        <th>Target Hari / Waktu</th>
                        <th>Status Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $item)
                        <tr class="hover">
                            <td>
                                <div class="font-bold text-sm">{{ $item->title }}</div>
                                @if($item->description)
                                    <div class="text-xs opacity-60 line-clamp-1 max-w-sm">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm">{{ $item->category->name ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-outline badge-sm uppercase font-bold text-xs">{{ $item->frequency }}</span>
                            </td>
                            <td>
                                <span class="text-xs opacity-70">{{ $item->target_day ?: 'Sesuai Jadwal' }}</span>
                            </td>
                            <td>
                                <button wire:click="toggleActive({{ $item->id }})" class="btn btn-xs {{ $item->is_active ? 'btn-success' : 'btn-ghost opacity-50' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $item->id }})" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-warning" />
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus jadwal rutin ini?" class="btn btn-ghost btn-xs btn-square text-error" title="Hapus">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 opacity-50">Tidak ada jadwal rutin ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $schedules->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <!-- Modal Form Jadwal Rutin -->
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-md p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <x-mary-icon name="o-calendar" class="text-primary" />
            {{ $scheduleId ? 'Edit Jadwal Rutin' : 'Tambah Jadwal Rutin Baru' }}
        </h3>
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Judul Agenda Pemeliharaan *</label>
                <x-mary-input wire:model="title" placeholder="Contoh: Pembersihan debu rak server utama" />
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Kategori IT *</label>
                <select wire:model="category_id" class="select select-bordered w-full">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Frekuensi *</label>
                    <select wire:model="frequency" class="select select-bordered w-full">
                        <option value="daily">Harian (Daily)</option>
                        <option value="weekly">Mingguan (Weekly)</option>
                        <option value="monthly">Bulanan (Monthly)</option>
                        <option value="quarterly">Triwulan (Quarterly)</option>
                        <option value="yearly">Tahunan (Yearly)</option>
                    </select>
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Target Hari</label>
                    <x-mary-input wire:model="target_day" placeholder="Contoh: Setiap Jumat" />
                </div>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Deskripsi / Detail Pemeliharaan</label>
                <textarea wire:model="description" rows="2" class="textarea textarea-bordered w-full" placeholder="Langkah-langkah yang harus diperiksa..."></textarea>
            </div>
            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" wire:model="is_active" class="checkbox checkbox-primary checkbox-sm" />
                    <span class="label-text font-medium text-sm">Aktifkan Jadwal Ini</span>
                </label>
            </div>
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md">Simpan</button>
            </div>
        </form>
    </x-mary-modal>
</div>