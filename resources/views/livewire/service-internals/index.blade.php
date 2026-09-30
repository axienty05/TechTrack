<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-2xl bg-primary/10 text-primary border border-primary/20 shadow-sm flex items-center justify-center shrink-0">
                <x-mary-icon name="o-wrench-screwdriver" class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Service Internal</h1>
                <p class="text-xs sm:text-sm opacity-60">Pencatatan perbaikan barang IT oleh tim internal IT sendiri</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" wire:click="openPrintModal" class="btn btn-outline btn-primary shadow-sm flex-1 sm:flex-initial font-medium gap-2">
                <x-mary-icon name="o-printer" class="w-4 h-4" />
                Cetak Laporan
            </button>
            <x-mary-button label="Catat Service Internal" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg shadow-primary/20 flex-1 sm:flex-initial font-medium" />
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Service --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5 hover:border-primary/20 transition-all">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-wrench-screwdriver" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight">{{ $stats['total'] }}</div>
                <div class="text-xs opacity-60 font-medium">Total Service</div>
            </div>
        </div>

        {{-- Sedang Dikerjakan --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5 hover:border-amber-500/20 transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-arrow-path" class="w-6 h-6 {{ $stats['proses'] > 0 ? 'animate-spin' : '' }}" style="animation-duration: 4s;" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-amber-500">{{ $stats['proses'] }}</div>
                <div class="text-xs opacity-60 font-medium">Sedang Dikerjakan</div>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5 hover:border-emerald-500/20 transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-check-circle" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-emerald-500">{{ $stats['selesai'] }}</div>
                <div class="text-xs opacity-60 font-medium">Selesai Diperbaiki</div>
            </div>
        </div>

        {{-- Unit UPS Terdata --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5 hover:border-sky-500/20 transition-all">
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-500 border border-sky-500/20 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-cpu-chip" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-sky-500">{{ $stats['total_ups'] }}</div>
                <div class="text-xs opacity-60 font-medium">Unit UPS Terdata</div>
            </div>
        </div>
    </div>

    {{-- Filters & Search Bar --}}
    <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center flex-1 min-w-[280px]">
            {{-- Search --}}
            <div class="w-full sm:w-72">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari barang, pemakai, kerusakan..." icon="o-magnifying-glass" clearable class="input-sm" />
            </div>

            {{-- Filter Status --}}
            <div class="w-full sm:w-44">
                <select wire:model.live="filterStatus" class="select select-bordered select-sm w-full font-medium">
                    <option value="">Semua Status</option>
                    <option value="proses">Sedang Dikerjakan</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>

            {{-- Filter Tahun --}}
            <div class="w-full sm:w-36">
                <select wire:model.live="filterYear" class="select select-bordered select-sm w-full font-medium">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            @if($search || $filterStatus || $filterYear)
                <button wire:click="resetFilters" class="btn btn-ghost btn-xs gap-1 text-xs opacity-70 hover:opacity-100">
                    <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" />
                    Reset Filter
                </button>
            @endif
        </div>

        <div class="text-xs opacity-50 font-mono">
            Total <span class="font-bold text-base-content">{{ $services->total() }}</span> catatan
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50 text-xs uppercase tracking-wider text-base-content/70">
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Barang</th>
                        <th>Pemakai</th>
                        <th>Tgl Mulai</th>
                        <th>Tgl Selesai</th>
                        <th>Kerusakan</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-content/5">
                    @forelse($services as $idx => $s)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            {{-- No --}}
                            <td class="font-mono text-xs opacity-50 text-center">
                                {{ $services->firstItem() + $idx }}
                            </td>

                            {{-- Barang (Nama + Serial Number) --}}
                            <td>
                                <div class="text-xs font-semibold text-base-content whitespace-nowrap">
                                    {{ $s->barang?->nama_barang ?? 'Barang tidak ditemukan' }}
                                </div>
                                @if($s->barang?->serial_number)
                                    <div class="inline-flex items-center gap-1 font-mono text-[10px] text-primary/80 mt-0.5 whitespace-nowrap">
                                        <x-mary-icon name="o-qr-code" class="w-2.5 h-2.5 opacity-50 shrink-0" />
                                        <span>{{ $s->barang->serial_number }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Pemakai (Tanpa inisial logo) --}}
                            <td>
                                @if($s->pemakai)
                                    <div>
                                        <div class="text-xs font-bold text-base-content">{{ $s->pemakai->nama }}</div>
                                        @if($s->pemakai->department)
                                            <div class="text-[10px] opacity-50">{{ $s->pemakai->department->name }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] bg-base-200/60 opacity-60">
                                        Tanpa Pemakai
                                    </span>
                                @endif
                            </td>

                            {{-- Tgl Mulai --}}
                            <td>
                                <div class="text-xs font-medium flex items-center gap-1.5">
                                    <x-mary-icon name="o-calendar" class="w-3.5 h-3.5 opacity-40 shrink-0" />
                                    <span class="font-mono">{{ $s->tgl_service ? $s->tgl_service->format('d M Y') : '-' }}</span>
                                </div>
                            </td>

                            {{-- Tgl Selesai (Hanya tanggal, tanpa teks 'Hari yang sama') --}}
                            <td>
                                @if($s->tgl_selesai)
                                    <div class="text-xs font-medium flex items-center gap-1.5 text-emerald-500 font-mono">
                                        <x-mary-icon name="o-check-circle" class="w-3.5 h-3.5 shrink-0" />
                                        <span>{{ $s->tgl_selesai->format('d M Y') }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Belum Selesai
                                    </span>
                                @endif
                            </td>

                            {{-- Kerusakan (Hanya teks sebelum >> biar pendek) --}}
                            <td>
                                @php
                                    $shortKerusakan = trim(explode('>>', $s->kerusakan)[0]);
                                @endphp
                                <div class="text-xs text-base-content/80 font-medium" title="{{ $s->kerusakan }}">
                                    {{ $shortKerusakan }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($s->tgl_selesai)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border bg-emerald-500/10 text-emerald-500 border-emerald-500/20 shadow-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border bg-amber-500/10 text-amber-500 border-amber-500/20 shadow-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Sedang Dikerjakan
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $s->id }})" class="btn btn-ghost btn-circle btn-xs text-warning hover:bg-warning/10" title="Edit Service">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $s->id }})" wire:confirm="Hapus catatan service internal untuk '{{ $s->barang?->nama_barang }}'?" class="btn btn-ghost btn-circle btn-xs text-error hover:bg-error/10" title="Hapus Service">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="space-y-3 max-w-sm mx-auto">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-base-200 flex items-center justify-center text-base-content/40">
                                        <x-mary-icon name="o-wrench-screwdriver" class="w-6 h-6" />
                                    </div>
                                    <div class="text-sm font-semibold text-base-content/80">Belum ada catatan service internal</div>
                                    <div class="text-xs opacity-50">
                                        {{ $search || $filterStatus || $filterYear ? 'Tidak ada data yang sesuai dengan filter pencarian.' : 'Klik tombol "Catat Service Internal" di atas untuk menambahkan catatan perbaikan baru.' }}
                                    </div>
                                    @if($search || $filterStatus || $filterYear)
                                        <button wire:click="resetFilters" class="btn btn-xs btn-outline btn-primary mt-2">
                                            Reset Filter
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30 flex items-center justify-between">
            <div class="text-xs opacity-50">
                Halaman {{ $services->currentPage() }} dari {{ $services->lastPage() }}
            </div>
            <div>
                {{ $services->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>

    {{-- Modal Form Service Internal --}}
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-xl p-5 sm:p-7 w-full max-h-[92vh] overflow-y-auto" without-trap-focus>
        <div class="flex items-center gap-3 pb-3 border-b border-base-content/10 mb-4">
            <div class="p-2.5 rounded-xl bg-primary/10 text-primary border border-primary/20">
                <x-mary-icon name="o-wrench-screwdriver" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="font-bold text-base text-base-content">
                    {{ $serviceId ? 'Edit Catatan Service Internal' : 'Catat Service Internal Baru' }}
                </h3>
                <p class="text-xs opacity-60">Lengkapi data perbaikan perangkat internal IT</p>
            </div>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- PEMAKAI DULU (searchable) --}}
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70 h-6 flex items-center">
                        Pemakai *
                    </label>
                    <x-mary-choices-offline
                        wire:model.live="m_pemakai_id"
                        :options="$pemakaiOptions"
                        single
                        searchable
                        clearable
                        placeholder="Cari & pilih pemakai..."
                    />
                    @error('m_pemakai_id') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- BARANG (difilter berdasarkan pemakai & kategori UPS) --}}
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70 h-6 flex items-center justify-between">
                        <span>Barang *</span>
                        @if($m_pemakai_id && $barangs->count() === 1)
                            <span class="text-[10px] text-emerald-500 font-semibold normal-case tracking-normal flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                (Otomatis Terpilih)
                            </span>
                        @endif
                    </label>
                    @if(!$m_pemakai_id)
                        <div class="input input-bordered w-full flex items-center text-xs opacity-40 italic bg-base-200/50">
                            ← Pilih pemakai terlebih dahulu
                        </div>
                    @else
                        <select wire:model.live="m_barang_id" class="select select-bordered w-full text-xs" style="font-size: 12px;">
                            <option value="" style="font-size: 12px;">-- Pilih Barang UPS --</option>
                            @forelse($barangs as $b)
                                <option value="{{ $b->id }}" style="font-size: 12px;">{{ $b->nama_barang }}{{ $b->serial_number ? ' (SN: ' . $b->serial_number . ')' : '' }}</option>
                            @empty
                                <option value="" disabled style="font-size: 12px;">Tidak ada UPS milik pemakai ini</option>
                            @endforelse
                        </select>
                    @endif
                    @error('m_barang_id') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70 h-6 flex items-center">
                        Tgl Mulai Pengerjaan *
                    </label>
                    <input type="date" wire:model="tgl_service" class="input input-bordered w-full text-sm font-mono" />
                    @error('tgl_service') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70 h-6 flex items-center justify-between">
                        <span>Tgl Selesai</span>
                        <span class="text-[10px] font-normal opacity-50 normal-case tracking-normal">(Kosongkan jika masih proses)</span>
                    </label>
                    <input type="date" wire:model="tgl_selesai" class="input input-bordered w-full text-sm font-mono" />
                    @error('tgl_selesai') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Deskripsi Kerusakan & Tindakan *</label>
                <textarea wire:model="kerusakan" class="textarea textarea-bordered w-full h-24 text-sm" placeholder="Jelaskan gejala kerusakan, diagnosa masalah, dan tindakan perbaikan yang dilakukan..."></textarea>
                @error('kerusakan') <span class="text-xs text-error mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md" wire:loading.attr="disabled">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    Simpan Data
                </button>
            </div>
        </form>
    </x-mary-modal>

    <!-- ======================================================= -->
    <!-- MODAL: CETAK & EXPORT LAPORAN SERVICE INTERNAL -->
    <!-- ======================================================= -->
    <x-mary-modal wire:model="showPrintModal" class="backdrop-blur-sm" box-class="max-w-md p-4 sm:p-6 w-full" without-trap-focus>
        <div class="flex items-center gap-2.5 pb-3 mb-4 border-b border-base-content/10 pr-8">
            <div class="p-2.5 rounded-xl bg-primary/10 text-primary shrink-0">
                <x-mary-icon name="o-printer" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="font-bold text-base sm:text-lg leading-tight">Cetak Laporan Service Internal</h3>
                <p class="text-xs text-base-content/60 mt-0.5">Pilih periode tahun dan format laporan</p>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Pilih Tahun Laporan --}}
            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70 py-1">Pilih Berdasarkan Tahun</label>
                <select wire:model.live="printYear" class="select select-bordered select-sm w-full font-medium">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Pengerjaan --}}
            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70 py-1">Filter Status</label>
                <select wire:model.live="printStatus" class="select select-bordered select-sm w-full font-medium">
                    <option value="">Semua Status (Selesai & Sedang Dikerjakan)</option>
                    <option value="selesai">Hanya yang Selesai</option>
                    <option value="proses">Hanya yang Sedang Dikerjakan</option>
                </select>
            </div>

            {{-- Ringkasan Pilihan --}}
            <div class="p-3 rounded-xl bg-base-200/50 border border-base-content/5 text-xs">
                <div class="font-semibold text-base-content flex items-center gap-1.5">
                    <x-mary-icon name="o-information-circle" class="w-4 h-4 text-primary" />
                    <span>Laporan yang akan dihasilkan:</span>
                </div>
                <div class="mt-1 opacity-70">
                    Periode: <strong class="text-base-content">{{ $printYear ? 'Tahun ' . $printYear : 'Semua Tahun' }}</strong>
                    &bull; Status: <strong class="text-base-content">{{ $printStatus === 'selesai' ? 'Selesai' : ($printStatus === 'proses' ? 'Sedang Dikerjakan' : 'Semua Status') }}</strong>
                </div>
            </div>

            {{-- Format Cetak / Unduh --}}
            <div class="pt-3 border-t border-base-content/10 space-y-2">
                <div class="text-[11px] font-bold uppercase tracking-wider opacity-70 mb-2">Pilih Format Cetak / Unduh:</div>
                <div class="grid grid-cols-2 gap-3">
                    {{-- Tombol PDF --}}
                    <a href="{{ $this->printUrl }}" target="_blank"
                        class="btn btn-primary btn-sm gap-2 w-full shadow-md font-semibold">
                        <x-mary-icon name="o-document-text" class="w-4 h-4" />
                        <span>Cetak / PDF</span>
                    </a>

                    {{-- Tombol Excel --}}
                    <button type="button" wire:click="exportExcel"
                        class="btn btn-success btn-sm gap-2 w-full shadow-md text-white font-semibold">
                        <x-mary-icon name="o-arrow-down-tray" class="w-4 h-4" />
                        <span>Export Excel</span>
                    </button>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="button" wire:click="$set('showPrintModal', false)" class="btn btn-ghost btn-xs opacity-60 hover:opacity-100">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </x-mary-modal>
</div>
