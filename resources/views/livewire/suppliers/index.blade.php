<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-xl bg-primary/10 text-primary">
                    <x-mary-icon name="o-truck" class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Master Supplier</h1>
                    <p class="text-xs sm:text-sm opacity-60">Kelola daftar supplier/vendor pengadaan perangkat IT</p>
                </div>
            </div>
        </div>
        <x-mary-button label="Tambah Supplier" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg shadow-primary/20 w-full sm:w-auto" />
    </div>

    {{-- Filters Bar --}}
    <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex flex-wrap gap-3 items-center justify-between">
        <div class="w-full sm:w-72">
            <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, telp, CP..." icon="o-magnifying-glass" clearable class="input-sm" />
        </div>
        @if($search)
            <button wire:click="$set('search', '')" class="btn btn-ghost btn-xs gap-1 text-xs opacity-70 hover:opacity-100">
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
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Contact Person</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-content/5">
                    @forelse($suppliers as $idx => $supplier)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="font-mono text-xs opacity-50 text-center">{{ $suppliers->firstItem() + $idx }}</td>
                            <td>
                                <div class="font-bold text-sm text-base-content">{{ $supplier->nama_supplier }}</div>
                            </td>
                            <td class="text-xs max-w-[200px] truncate opacity-70">{{ $supplier->alamat ?? '-' }}</td>
                            <td class="text-xs font-mono opacity-80">{{ $supplier->no_telp ?? '-' }}</td>
                            <td class="text-xs opacity-80">{{ $supplier->email ?? '-' }}</td>
                            <td class="text-xs opacity-80">{{ $supplier->cp ?? '-' }}{{ $supplier->no_hp ? ' ('.$supplier->no_hp.')' : '' }}</td>
                            <td>
                                @if($supplier->status)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
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
                                    <button wire:click="openEditModal({{ $supplier->id }})" class="btn btn-ghost btn-circle btn-xs text-warning hover:bg-warning/10" title="Edit Supplier">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $supplier->id }})" wire:confirm="Hapus supplier '{{ $supplier->nama_supplier }}'?" class="btn btn-ghost btn-circle btn-xs text-error hover:bg-error/10" title="Hapus Supplier">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-base-200 flex items-center justify-center text-base-content/40 mb-1">
                                        <x-mary-icon name="o-truck" class="w-6 h-6" />
                                    </div>
                                    <div class="font-bold text-base opacity-70">Belum Ada Data Supplier</div>
                                    <p class="text-xs opacity-50 max-w-sm">Data supplier belum ditambahkan atau tidak sesuai dengan kata kunci pencarian.</p>
                                    <x-mary-button label="Tambah Supplier Pertama" icon="o-plus" wire:click="openCreateModal" class="btn-primary btn-sm mt-3" />
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $suppliers->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- Modal Form Supplier --}}
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-lg p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4">
            <h3 class="font-extrabold text-lg flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                    <x-mary-icon name="o-truck" class="w-5 h-5" />
                </div>
                <span>{{ $supplierId ? 'Edit Data Supplier' : 'Tambah Supplier Baru' }}</span>
            </h3>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Nama Supplier *</label>
                <x-mary-input wire:model="nama_supplier" placeholder="Contoh: PT. Jaya Komputer" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">No. Telepon</label>
                    <x-mary-input wire:model="no_telp" placeholder="021-12345678" class="font-mono text-xs" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Email</label>
                    <x-mary-input wire:model="email" type="email" placeholder="supplier@email.com" />
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Alamat</label>
                <x-mary-input wire:model="alamat" placeholder="Jl. Raya No. 123, Jakarta" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Contact Person (CP)</label>
                    <x-mary-input wire:model="cp" placeholder="Nama CP" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">No. HP (CP)</label>
                    <x-mary-input wire:model="no_hp" placeholder="08123456789" class="font-mono text-xs" />
                </div>
            </div>

            <div class="p-3 rounded-xl bg-base-200/50 border border-base-content/5">
                <label class="flex items-center justify-between cursor-pointer">
                    <div class="space-y-0.5">
                        <div class="text-xs font-bold uppercase opacity-80">Status Supplier</div>
                        <div class="text-[11px] opacity-60">Status aktif membolehkan pencatatan mutasi barang</div>
                    </div>
                    <input type="checkbox" wire:model="status" class="toggle toggle-primary toggle-sm" />
                </label>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md shadow-primary/20 w-full sm:w-auto">
                    Simpan Supplier
                </button>
            </div>
        </form>
    </x-mary-modal>
</div>
