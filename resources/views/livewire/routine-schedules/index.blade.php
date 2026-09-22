<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Jadwal Pemeliharaan Rutin</h1>
            <p class="text-xs sm:text-sm opacity-60">Daftar agenda pemeliharaan preventif (maintenance berkala) infrastruktur IT</p>
        </div>
        <div class="w-full sm:w-auto">
            <x-mary-button label="Tambah Jadwal Rutin" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg w-full sm:w-auto" />
        </div>
    </div>

    <div class="bg-base-100 p-3 sm:p-4 rounded-2xl shadow-md border border-base-content/5 flex flex-wrap gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari judul jadwal..." icon="o-magnifying-glass" clearable />
        </div>
    </div>

    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-sm sm:table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="whitespace-nowrap">Nama Agenda / Tugas</th>
                        <th class="whitespace-nowrap">Kategori</th>
                        <th class="whitespace-nowrap">Lokasi PC</th>
                        <th class="whitespace-nowrap">Frekuensi</th>
                        <th class="whitespace-nowrap">Target Hari / Waktu</th>
                        <th class="whitespace-nowrap">Status Aktif</th>
                        <th class="text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $item)
                        <tr class="hover">
                            <td>
                                <div class="font-bold text-sm whitespace-nowrap">{{ $item->title }}</div>
                                @if($item->description)
                                    <div class="text-xs opacity-60 line-clamp-1 max-w-xs sm:max-w-sm">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-base-200/80 border border-base-content/10 text-xs font-medium text-base-content">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                                    <span>{{ $item->category->name ?? '-' }}</span>
                                </span>
                            </td>
                            <td class="whitespace-nowrap">
                                @php
                                    $locStyle = match($item->pc_location) {
                                        'kantor' => 'bg-info/10 text-info border-info/20',
                                        'pabrik' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
                                        default  => 'bg-base-200 text-base-content/70 border-base-content/10',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-xs font-semibold {{ $locStyle }}">
                                    {{ $item->pc_location_label }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap">
                                @php
                                    $freqStyle = match($item->frequency) {
                                        'daily'                     => 'bg-info/10 text-info border-info/20',
                                        'weekly'                    => 'bg-accent/10 text-accent border-accent/20',
                                        'monthly'                   => 'bg-primary/10 text-primary border-primary/20',
                                        'quarterly'                 => 'bg-secondary/10 text-secondary border-secondary/20',
                                        'semester', 'semi_annually' => 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20',
                                        'yearly'                    => 'bg-purple-500/10 text-purple-600 border-purple-500/20',
                                        default                     => 'bg-base-200 text-base-content/80 border-base-content/10',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-semibold {{ $freqStyle }}">
                                    <x-mary-icon name="o-arrow-path" class="w-3.5 h-3.5 shrink-0" />
                                    <span>{{ $item->frequency_label }}</span>
                                </span>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="text-xs opacity-75 font-medium">{{ $item->target_day ?: 'Sesuai Jadwal' }}</span>
                            </td>
                            <td>
                                <button wire:click="toggleActive({{ $item->id }})" class="btn btn-xs {{ $item->is_active ? 'btn-success' : 'btn-ghost opacity-50' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="text-right whitespace-nowrap">
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
                            <td colspan="7" class="text-center py-8 opacity-50 text-sm">Tidak ada jadwal rutin ditemukan</td>
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
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-md p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <h3 class="font-bold text-base sm:text-lg mb-4 flex items-center gap-2">
            <x-mary-icon name="o-calendar" class="text-primary w-5 h-5 flex-shrink-0" />
            <span>{{ $scheduleId ? 'Edit Jadwal Rutin' : 'Tambah Jadwal Rutin Baru' }}</span>
        </h3>
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Judul Agenda Pemeliharaan *</label>
                <x-mary-input wire:model="title" placeholder="Contoh: Pembersihan debu rak server utama" />
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Kategori IT *</label>
                <select wire:model="category_id" class="select select-bordered w-full select-sm">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Frekuensi *</label>
                    <select wire:model="frequency" class="select select-bordered w-full select-sm">
                        <option value="daily">Harian (Daily)</option>
                        <option value="weekly">Mingguan (Weekly)</option>
                        <option value="monthly">Bulanan (Monthly)</option>
                        <option value="quarterly">Triwulan (3 Bulan)</option>
                        <option value="semester">Semester (6 Bulan)</option>
                        <option value="yearly">Tahunan (Yearly)</option>
                    </select>
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Target Hari</label>
                    <x-mary-input wire:model="target_day" placeholder="Contoh: Setiap Jumat" />
                </div>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Berlaku untuk Lokasi PC *</label>
                <select wire:model="pc_location" class="select select-bordered w-full select-sm">
                    <option value="semua">Semua Lokasi (Kantor & Pabrik)</option>
                    <option value="kantor">Kantor</option>
                    <option value="pabrik">Pabrik</option>
                </select>
                <p class="text-xs opacity-50 mt-1">Jadwal ini akan dipakai sebagai acuan due date maintenance PC di lokasi yang dipilih.</p>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Deskripsi / Detail Pemeliharaan</label>
                <textarea wire:model="description" rows="2" class="textarea textarea-bordered w-full text-sm" placeholder="Langkah-langkah yang harus diperiksa..."></textarea>
            </div>
            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" wire:model="is_active" class="checkbox checkbox-primary checkbox-sm" />
                    <span class="label-text font-medium text-sm">Aktifkan Jadwal Ini</span>
                </label>
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md w-full sm:w-auto">Simpan</button>
            </div>
        </form>
    </x-mary-modal>
</div>