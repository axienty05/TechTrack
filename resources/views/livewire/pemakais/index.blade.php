<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-xl bg-primary/10 text-primary">
                    <x-mary-icon name="o-user-group" class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Master Pemakai</h1>
                    <p class="text-xs sm:text-sm opacity-60">Kelola daftar karyawan pemakai/pengguna perangkat IT</p>
                </div>
            </div>
        </div>
        <x-mary-button label="Tambah Pemakai" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg shadow-primary/20 w-full sm:w-auto" />
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <x-mary-icon name="o-users" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight">{{ $stats['total'] }}</div>
                <div class="text-xs opacity-60 font-medium">Total Pemakai</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-check-circle" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-emerald-600 dark:text-emerald-400">{{ $stats['aktif'] }}</div>
                <div class="text-xs opacity-60 font-medium">Pemakai Aktif</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-computer-desktop" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-indigo-600 dark:text-indigo-400">{{ $stats['komputer'] }}</div>
                <div class="text-xs opacity-60 font-medium">Komputer Terhubung</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-cube" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-amber-600 dark:text-amber-400">{{ $stats['barangs'] }}</div>
                <div class="text-xs opacity-60 font-medium">Barang Dipegang</div>
            </div>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center flex-1 min-w-[280px]">
            <div class="w-full sm:w-72">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari nama pemakai / PC..." icon="o-magnifying-glass" clearable class="input-sm" />
            </div>
            <div class="w-full sm:w-48">
                <select wire:model.live="filterDepartment" class="select select-bordered select-sm w-full">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-36">
                <select wire:model.live="filterStatus" class="select select-bordered select-sm w-full">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>

        @if($search || $filterDepartment || $filterStatus !== '')
            <button wire:click="$set('search', ''); $set('filterDepartment', ''); $set('filterStatus', '')" class="btn btn-ghost btn-xs gap-1 text-xs opacity-70 hover:opacity-100">
                <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" />
                Reset Filter
            </button>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/40 text-xs uppercase tracking-wider opacity-70">
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Nama Pemakai</th>
                        <th>Departemen</th>
                        <th>Komputer</th>
                        <th>Jumlah Barang</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-content/5">
                    @forelse($pemakais as $idx => $pemakai)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="font-mono text-xs opacity-50 text-center">{{ $pemakais->firstItem() + $idx }}</td>
                            <td>
                                <div class="font-bold text-sm text-base-content">{{ $pemakai->nama }}</div>
                            </td>
                            <td>
                                @if($pemakai->department)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                        {{ $pemakai->department->code ?? $pemakai->department->name }}
                                    </span>
                                @else
                                    <span class="text-xs opacity-30 font-mono">-</span>
                                @endif
                            </td>
                            <td>
                                @if($pemakai->comp_name || $pemakai->computerDevices->count() > 0)
                                    <div class="flex flex-wrap gap-1.5 items-center">
                                        @if($pemakai->comp_name)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 font-mono text-xs font-bold">
                                                <x-mary-icon name="o-computer-desktop" class="w-3.5 h-3.5 shrink-0" />
                                                {{ $pemakai->comp_name }}
                                            </span>
                                        @endif
                                        @foreach($pemakai->computerDevices as $device)
                                            @if($device->comp_name !== $pemakai->comp_name)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-base-200 text-base-content/70 font-mono text-xs font-medium border border-base-content/10">
                                                    <x-mary-icon name="o-computer-desktop" class="w-3 h-3 opacity-60 shrink-0" />
                                                    {{ $device->comp_name }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs opacity-30 font-mono">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-base-200 text-base-content/80">
                                    <x-mary-icon name="o-cube" class="w-3.5 h-3.5 opacity-60" />
                                    {{ $pemakai->barangs_count }} barang
                                </span>
                            </td>
                            <td>
                                @if($pemakai->status)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $pemakai->id }})" class="btn btn-ghost btn-circle btn-xs text-warning hover:bg-warning/10" title="Edit Pemakai">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $pemakai->id }})" wire:confirm="Hapus pemakai '{{ $pemakai->nama }}'? Pastikan tidak ada barang yang terhubung." class="btn btn-ghost btn-circle btn-xs text-error hover:bg-error/10" title="Hapus Pemakai">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-base-200 flex items-center justify-center text-base-content/40 mb-1">
                                        <x-mary-icon name="o-user-group" class="w-6 h-6" />
                                    </div>
                                    <div class="font-bold text-base opacity-70">Belum Ada Data Pemakai</div>
                                    <p class="text-xs opacity-50 max-w-sm">Data karyawan pemakai belum ditambahkan atau tidak sesuai dengan kata kunci pencarian.</p>
                                    <x-mary-button label="Tambah Pemakai Pertama" icon="o-plus" wire:click="openCreateModal" class="btn-primary btn-sm mt-3" />
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $pemakais->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- Modal Form Pemakai --}}
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-md p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4">
            <h3 class="font-extrabold text-lg flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                    <x-mary-icon name="o-user-group" class="w-5 h-5" />
                </div>
                <span>{{ $pemakaiId ? 'Edit Data Pemakai' : 'Tambah Pemakai Baru' }}</span>
            </h3>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Nama Pemakai *</label>
                <x-mary-input wire:model="nama" placeholder="Contoh: Budi Santoso" />
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Nama Komputer (Comp Name)</label>
                <x-mary-input wire:model="comp_name" placeholder="Contoh: ACCT-01, DESKTOP-RU1L62Q..." class="font-mono text-xs" />
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Departemen</label>
                <select wire:model="department_id" class="select select-bordered w-full">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-3 rounded-xl bg-base-200/50 border border-base-content/5">
                <label class="flex items-center justify-between cursor-pointer">
                    <div class="space-y-0.5">
                        <div class="text-xs font-bold uppercase opacity-80">Status Pemakai</div>
                        <div class="text-[11px] opacity-60">Status aktif membolehkan penugasan barang IT</div>
                    </div>
                    <input type="checkbox" wire:model="status" class="toggle toggle-primary toggle-sm" />
                </label>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md shadow-primary/20 w-full sm:w-auto">
                    Simpan Pemakai
                </button>
            </div>
        </form>
    </x-mary-modal>
</div>
