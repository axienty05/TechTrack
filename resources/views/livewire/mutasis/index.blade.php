<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-xl bg-primary/10 text-primary">
                    <x-mary-icon name="o-arrows-right-left" class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Mutasi Barang</h1>
                    <p class="text-xs sm:text-sm opacity-60">Kelola perpindahan, pembelian, dan penjualan aset IT</p>
                </div>
            </div>
        </div>
        <x-mary-button label="Tambah Mutasi" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg shadow-primary/20 w-full sm:w-auto" />
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <x-mary-icon name="o-arrows-right-left" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight">{{ $stats['total'] }}</div>
                <div class="text-xs opacity-60 font-medium">Total Mutasi</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-shopping-cart" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-emerald-600 dark:text-emerald-400">{{ $stats['pembelian'] }}</div>
                <div class="text-xs opacity-60 font-medium">Pembelian Baru</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-arrow-path" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-indigo-600 dark:text-indigo-400">{{ $stats['perpindahan'] }}</div>
                <div class="text-xs opacity-60 font-medium">Perpindahan Pemakai</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-banknotes" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-rose-600 dark:text-rose-400">{{ $stats['penjualan'] }}</div>
                <div class="text-xs opacity-60 font-medium">Penjualan Aset</div>
            </div>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center flex-1 min-w-[280px]">
            <div class="w-full sm:w-64">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari no. mutasi, supplier..." icon="o-magnifying-glass" clearable class="input-sm" />
            </div>
            <div class="w-full sm:w-44">
                <select wire:model.live="filterJenis" class="select select-bordered select-sm w-full">
                    <option value="">Semua Jenis</option>
                    <option value="pembelian">Pembelian</option>
                    <option value="penjualan">Penjualan</option>
                    <option value="perpindahan">Perpindahan</option>
                </select>
            </div>
            <div class="w-full sm:w-44">
                <input type="month" wire:model.live="filterBulan" class="input input-bordered input-sm w-full" />
            </div>
        </div>

        @if($search || $filterJenis || $filterBulan)
            <button wire:click="$set('search', ''); $set('filterJenis', ''); $set('filterBulan', '')" class="btn btn-ghost btn-xs gap-1 text-xs opacity-70 hover:opacity-100">
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
                        <th>No. Mutasi</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Jumlah Barang</th>
                        <th>Keterangan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-content/5">
                    @forelse($mutasis as $mutasi)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-primary/10 text-primary border border-primary/20 font-mono font-extrabold text-xs">
                                    {{ $mutasi->no_mutasi }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $jenisBadges = [
                                        'pembelian' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                        'penjualan' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                        'perpindahan' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $jenisBadges[$mutasi->jenis_mutasi] ?? 'bg-base-200' }}">
                                    {{ ucfirst($mutasi->jenis_mutasi) }}
                                </span>
                            </td>
                            <td class="text-xs font-mono opacity-80">{{ $mutasi->tgl_mutasi->format('d/m/Y') }}</td>
                            <td>
                                @if($mutasi->supplier)
                                    <div class="text-xs font-bold text-base-content">{{ $mutasi->supplier->nama_supplier }}</div>
                                @else
                                    <span class="text-xs opacity-30 font-mono">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-base-200 text-base-content/80">
                                    <x-mary-icon name="o-cube" class="w-3.5 h-3.5 opacity-60" />
                                    {{ $mutasi->dt_mutasis_count }} item
                                </span>
                            </td>
                            <td class="text-xs max-w-[200px] truncate opacity-70">{{ $mutasi->keterangan ?? '-' }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openDetailModal({{ $mutasi->id }})" class="btn btn-ghost btn-circle btn-xs text-info hover:bg-info/10" title="Lihat Detail Barang">
                                        <x-mary-icon name="o-eye" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="deleteMutasi({{ $mutasi->id }})" wire:confirm="Hapus mutasi '{{ $mutasi->no_mutasi }}'? Efek perubahan pemakai/status barang akan di-rollback." class="btn btn-ghost btn-circle btn-xs text-error hover:bg-error/10" title="Hapus Mutasi">
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
                                        <x-mary-icon name="o-arrows-right-left" class="w-6 h-6" />
                                    </div>
                                    <div class="font-bold text-base opacity-70">Belum Ada Data Mutasi</div>
                                    <p class="text-xs opacity-50 max-w-sm">Riwayat mutasi barang belum ditambahkan atau tidak sesuai dengan kata kunci pencarian.</p>
                                    <x-mary-button label="Tambah Mutasi Pertama" icon="o-plus" wire:click="openCreateModal" class="btn-primary btn-sm mt-3" />
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $mutasis->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- Modal Form Mutasi --}}
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-4xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4">
            <div>
                <h3 class="font-extrabold text-lg flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                        <x-mary-icon name="o-arrows-right-left" class="w-5 h-5" />
                    </div>
                    <span>Buat Mutasi Barang</span>
                </h3>
                <p class="text-xs opacity-60 mt-0.5">Form transaksi pengadaan, penjualan, atau mutasi perpindahan pemakai</p>
            </div>
        </div>

        <form wire:submit="save" class="space-y-6">
            {{-- Informasi Utama Mutasi --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-base-200/40 border border-base-content/10 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider opacity-70 border-b border-base-content/10 pb-2">Informasi Utama Mutasi</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Jenis Mutasi *</label>
                        <select wire:model.live="jenis_mutasi" class="select select-bordered select-sm w-full rounded-xl font-semibold">
                            <option value="pembelian">Pembelian / Pengadaan</option>
                            <option value="penjualan">Penjualan / Disposal</option>
                            <option value="perpindahan">Perpindahan Pemakai</option>
                        </select>
                    </div>
                    <div>
                        <label class="label text-xs font-bold uppercase tracking-wider opacity-70">No. Mutasi (Auto)</label>
                        <input type="text" value="{{ $no_mutasi_preview }}" disabled class="input input-bordered input-sm w-full rounded-xl bg-base-200/60 text-base-content/60 font-mono text-xs font-semibold" />
                    </div>
                    <div>
                        <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Tanggal Mutasi *</label>
                        <input type="date" wire:model="tgl_mutasi" class="input input-bordered input-sm w-full font-mono rounded-xl" />
                    </div>
                </div>

                @if($jenis_mutasi !== 'perpindahan')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Supplier / Vendor {{ $jenis_mutasi === 'pembelian' ? '*' : '' }}</label>
                            <x-mary-choices-offline
                                wire:model="m_supplier_id"
                                :options="$supplierOptions"
                                single
                                searchable
                                clearable
                                placeholder="Pilih atau ketik supplier..."
                            />
                        </div>
                        <div>
                            <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Keterangan</label>
                            <x-mary-input wire:model="keterangan" placeholder="Catatan transaksi..." class="input-sm rounded-xl" />
                        </div>
                    </div>
                @endif
            </div>

            {{-- Detail Items --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-base-200/40 border border-base-content/10 space-y-4">
                <div class="flex items-center justify-between border-b border-base-content/10 pb-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider opacity-70">Daftar Barang Mutasi</h4>
                    <button type="button" wire:click="addDetail" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold hover:bg-emerald-500/20 transition-colors">
                        <x-mary-icon name="o-plus" class="w-3.5 h-3.5" />
                        <span>Tambah Baris</span>
                    </button>
                </div>

                <div class="space-y-3">
                    @foreach($details as $idx => $item)
                        <div wire:key="modal-row-{{ $idx }}-{{ $item['m_barang_id'] ?? '' }}" class="p-3.5 rounded-xl bg-base-100 border border-base-content/10 flex flex-col md:flex-row items-center gap-3">
                            <div class="flex-1 w-full">
                                <label class="block text-[11px] font-bold uppercase tracking-wider opacity-60 mb-1">Pilih Barang #{{ $idx + 1 }} *</label>
                                <div wire:key="modal-barang-wrapper-{{ $idx }}-{{ $item['m_barang_id'] ?? 'none' }}-{{ implode('_', array_column($rowBarangOptions[$idx] ?? [], 'id')) }}">
                                    <x-mary-choices-offline
                                        :id="'modal_barang_' . $idx"
                                        wire:model.live="details.{{ $idx }}.m_barang_id"
                                        :options="$rowBarangOptions[$idx] ?? $barangOptions"
                                        single
                                        searchable
                                        clearable
                                        placeholder="Pilih atau ketik barang..."
                                    />
                                </div>
                                @error("details.{$idx}.m_barang_id") <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                            </div>

                            @if($jenis_mutasi === 'perpindahan')
                                <div class="w-full md:w-56">
                                    <label class="block text-[11px] font-bold uppercase tracking-wider opacity-60 mb-1">Pemakai Baru *</label>
                                    <x-mary-choices-offline
                                        :id="'modal_pemakai_' . $idx"
                                        wire:model="details.{{ $idx }}.pemakai_baru"
                                        :options="$pemakaiOptions"
                                        single
                                        searchable
                                        clearable
                                        placeholder="Pilih pemakai baru..."
                                    />
                                    @error("details.{$idx}.pemakai_baru") <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div class="w-full md:w-40">
                                    <label class="block text-[11px] font-bold uppercase tracking-wider opacity-60 mb-1">Harga (Rp)</label>
                                    <input type="number" wire:model="details.{{ $idx }}.harga" min="0" placeholder="0" class="input input-bordered input-sm w-full text-xs font-mono rounded-lg" />
                                </div>
                            @endif

                            @if(count($details) > 1)
                                <div class="pt-4 md:pt-5">
                                    <button type="button" wire:click="removeDetail({{ $idx }})" class="p-1.5 rounded-lg text-base-content/40 hover:text-error hover:bg-error/10 transition-colors" title="Hapus baris">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-2 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto rounded-xl">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md shadow-primary/20 w-full sm:w-auto rounded-xl font-bold" wire:loading.attr="disabled">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    Simpan Transaksi Mutasi
                </button>
            </div>
        </form>
    </x-mary-modal>
    {{-- Modal Detail Mutasi --}}
    <x-mary-modal wire:model="showDetailModal" class="backdrop-blur-sm" box-class="max-w-3xl p-4 sm:p-6 w-full max-h-[90vh] overflow-y-auto">
        @if($detailMutasi)
            @php
                $jenisBadgesDetail = [
                    'pembelian'   => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                    'penjualan'   => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                    'perpindahan' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                ];
                $jenisBadge = $jenisBadgesDetail[$detailMutasi->jenis_mutasi] ?? 'bg-base-200';
            @endphp

            {{-- Header Modal --}}
            <div class="flex items-center gap-3 pb-4 border-b border-base-content/10 mb-5">
                <div class="p-2 rounded-xl bg-primary/10 text-primary">
                    <x-mary-icon name="o-document-text" class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="font-extrabold text-lg tracking-tight">Detail Mutasi</h3>
                    <p class="text-xs opacity-50 mt-0.5">Rincian barang dalam transaksi ini</p>
                </div>
            </div>

            {{-- Info Utama --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                <div class="bg-base-200/50 rounded-xl p-3">
                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-50 mb-1">No. Mutasi</div>
                    <div class="font-mono font-extrabold text-xs text-primary">{{ $detailMutasi->no_mutasi }}</div>
                </div>
                <div class="bg-base-200/50 rounded-xl p-3">
                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-50 mb-1">Jenis</div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $jenisBadge }}">
                        {{ ucfirst($detailMutasi->jenis_mutasi) }}
                    </span>
                </div>
                <div class="bg-base-200/50 rounded-xl p-3">
                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-50 mb-1">Tanggal</div>
                    <div class="font-mono text-xs font-bold">{{ $detailMutasi->tgl_mutasi->format('d/m/Y') }}</div>
                </div>
                <div class="bg-base-200/50 rounded-xl p-3">
                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-50 mb-1">Supplier</div>
                    <div class="text-xs font-bold truncate">{{ $detailMutasi->supplier?->nama_supplier ?? '-' }}</div>
                </div>
            </div>

            @if($detailMutasi->keterangan)
                <div class="bg-base-200/40 rounded-xl px-4 py-2.5 mb-5 text-xs opacity-70">
                    <span class="font-bold opacity-60 uppercase text-[10px] tracking-wider mr-2">Keterangan:</span>
                    {{ $detailMutasi->keterangan }}
                </div>
            @endif

            {{-- Tabel Barang --}}
            <div class="rounded-xl overflow-hidden border border-base-content/10">
                <div class="px-4 py-2.5 bg-base-200/60 text-[11px] font-bold uppercase tracking-wider opacity-60 border-b border-base-content/10">
                    Daftar Barang ({{ $detailMutasi->dtMutasis->count() }} item)
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead class="bg-base-200/30 text-[10px] uppercase tracking-wider opacity-60">
                            <tr>
                                <th class="w-6">#</th>
                                <th>Nama Barang</th>
                                <th>Serial Number</th>
                                @if($detailMutasi->jenis_mutasi === 'perpindahan')
                                    <th>Pemakai Lama</th>
                                    <th>Pemakai Baru</th>
                                @else
                                    <th>Pemakai</th>
                                    <th class="text-right">Harga</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-base-content/5">
                            @foreach($detailMutasi->dtMutasis as $i => $dt)
                                <tr class="hover:bg-base-200/20 transition-colors">
                                    <td class="text-xs font-mono opacity-40">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="font-semibold text-xs">{{ $dt->barang?->nama_barang ?? '-' }}</div>
                                        <div class="text-[10px] opacity-50">{{ $dt->barang?->kode_barang ?? '' }}</div>
                                    </td>
                                    <td class="font-mono text-xs opacity-70">{{ $dt->barang?->serial_number ?? '-' }}</td>
                                    @if($detailMutasi->jenis_mutasi === 'perpindahan')
                                        <td class="text-xs">
                                            @php $pLama = $dt->pemakai_lama ? \App\Models\Pemakai::find($dt->pemakai_lama) : null; @endphp
                                            {{ $pLama?->nama ?? '-' }}
                                        </td>
                                        <td class="text-xs">
                                            @php $pBaru = $dt->pemakai_baru ? \App\Models\Pemakai::find($dt->pemakai_baru) : null; @endphp
                                            {{ $pBaru?->nama ?? '-' }}
                                        </td>
                                    @else
                                        <td class="text-xs">{{ $dt->barang?->pemakai?->nama ?? '-' }}</td>
                                        <td class="text-right">
                                            <span class="font-mono text-xs font-semibold">
                                                {{ $dt->harga ? 'Rp ' . number_format($dt->harga, 0, ',', '.') : '-' }}
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($detailMutasi->jenis_mutasi !== 'perpindahan')
                    @php
                        $totalHarga = $detailMutasi->dtMutasis->sum('harga');
                    @endphp
                    @if($totalHarga > 0)
                        <div class="px-4 py-3 bg-base-200/40 border-t border-base-content/10 flex justify-end">
                            <div class="text-right">
                                <div class="text-[10px] font-bold uppercase tracking-wider opacity-50">Total Harga</div>
                                <div class="font-mono font-black text-sm text-primary">Rp {{ number_format($totalHarga, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <div class="flex justify-end mt-4">
                <button wire:click="$set('showDetailModal', false)" class="btn btn-ghost btn-sm rounded-xl">Tutup</button>
            </div>
        @endif
    </x-mary-modal>
</div>
