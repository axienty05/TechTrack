<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-xl bg-primary/10 text-primary">
                    <x-mary-icon name="o-cube" class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Data Barang & Aset IT</h1>
                    <p class="text-xs sm:text-sm opacity-60">Kelola daftar seluruh perangkat dan aset IT perusahaan</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <x-mary-button label="Impor Excel" icon="o-arrow-up-tray" wire:click="openImportModal" class="btn-outline btn-primary shadow-sm w-full sm:w-auto text-xs sm:text-sm font-semibold" />
            <x-mary-button label="Tambah Barang" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg shadow-primary/20 w-full sm:w-auto text-xs sm:text-sm font-semibold" />
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <x-mary-icon name="o-cube" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight">{{ $stats['total'] }}</div>
                <div class="text-xs opacity-60 font-medium">Total Barang</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-check-circle" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-emerald-600 dark:text-emerald-400">{{ $stats['aktif'] }}</div>
                <div class="text-xs opacity-60 font-medium">Barang Aktif</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-wrench-screwdriver" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-amber-600 dark:text-amber-400">{{ $stats['sedang_service'] }}</div>
                <div class="text-xs opacity-60 font-medium">Sedang Service</div>
            </div>
        </div>

        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-exclamation-triangle" class="w-5 h-5" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-rose-600 dark:text-rose-400">{{ $stats['rusak'] }}</div>
                <div class="text-xs opacity-60 font-medium">Non-Aktif / Rusak</div>
            </div>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center flex-1 min-w-[280px]">
            <div class="w-full sm:w-64">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari kode, nama, SN, pemakai..." icon="o-magnifying-glass" clearable class="input-sm" />
            </div>
            <div class="w-full sm:w-40">
                <select wire:model.live="filterKategori" class="select select-bordered select-sm w-full">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}">{{ ucfirst(str_replace('_', ' ', $kat)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-36">
                <select wire:model.live="filterStatus" class="select select-bordered select-sm w-full">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $st)
                        <option value="{{ $st }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-44">
                <select wire:model.live="filterDepartment" class="select select-bordered select-sm w-full">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($search || $filterKategori || $filterStatus || $filterDepartment)
            <button wire:click="$set('search', ''); $set('filterKategori', ''); $set('filterStatus', ''); $set('filterDepartment', '')" class="btn btn-ghost btn-xs gap-1 text-xs opacity-70 hover:opacity-100">
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
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Serial Number</th>
                        <th>Pemakai</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-content/5">
                    @forelse($barangs as $idx => $barang)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="font-mono text-xs opacity-50 text-center">{{ $barangs->firstItem() + $idx }}</td>
                            <td>
                                <button type="button" wire:click="openHistoryModal({{ $barang->id }})" class="inline-flex items-center px-2.5 py-1 rounded-md bg-primary/10 text-primary border border-primary/20 font-mono font-extrabold text-xs hover:bg-primary/20 transition-colors" title="Klik untuk lihat riwayat lengkap barang">
                                    {{ $barang->kode_barang }}
                                </button>
                            </td>
                            <td>
                                <button type="button" wire:click="openHistoryModal({{ $barang->id }})" class="text-left font-bold text-sm text-base-content hover:text-primary transition-colors block" title="Klik untuk lihat riwayat lengkap barang">
                                    {{ $barang->nama_barang }}
                                </button>
                                @if($barang->keterangan)
                                    <div class="text-[11px] opacity-50 truncate max-w-[220px]">{{ $barang->keterangan }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-base-200 text-base-content/70">
                                    {{ ucfirst(str_replace('_', ' ', $barang->kategori)) }}
                                </span>
                            </td>
                            <td class="font-mono text-xs opacity-80">{{ $barang->serial_number ?? '-' }}</td>
                            <td>
                                @if($barang->pemakai)
                                    <div>
                                        <div class="text-xs font-bold text-base-content">{{ $barang->pemakai->nama }}</div>
                                        @if($barang->pemakai->department)
                                            <div class="text-[10px] opacity-50">{{ $barang->pemakai->department->code ?? $barang->pemakai->department->name }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] bg-base-200/60 opacity-50">
                                        Belum ada
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'aktif' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                        'tidak_aktif' => 'bg-base-200 text-base-content/60 border-base-content/10',
                                        'sedang_service' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                        'rusak' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                        'dijual' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusBadges[$barang->status] ?? 'bg-base-200' }}">
                                    @if($barang->status === 'aktif')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    @elseif($barang->status === 'sedang_service')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    @endif
                                    {{ ucfirst(str_replace('_', ' ', $barang->status)) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openHistoryModal({{ $barang->id }})" class="btn btn-ghost btn-circle btn-xs text-info hover:bg-info/10" title="Lihat Riwayat & Lifecycle Barang">
                                        <x-mary-icon name="o-clock" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="openEditModal({{ $barang->id }})" class="btn btn-ghost btn-circle btn-xs text-warning hover:bg-warning/10" title="Edit Barang">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $barang->id }})" wire:confirm="Hapus barang '{{ $barang->nama_barang }}' ({{ $barang->kode_barang }})?" class="btn btn-ghost btn-circle btn-xs text-error hover:bg-error/10" title="Hapus Barang">
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
                                        <x-mary-icon name="o-cube" class="w-6 h-6" />
                                    </div>
                                    <div class="font-bold text-base opacity-70">Belum Ada Data Barang</div>
                                    <p class="text-xs opacity-50 max-w-sm">Data aset barang belum ditambahkan atau tidak sesuai dengan kata kunci pencarian.</p>
                                    <x-mary-button label="Tambah Barang Pertama" icon="o-plus" wire:click="openCreateModal" class="btn-primary btn-sm mt-3" />
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $barangs->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- Modal Form Barang --}}
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-lg p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4">
            <h3 class="font-extrabold text-lg flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                    <x-mary-icon name="o-cube" class="w-5 h-5" />
                </div>
                <span>{{ $barangId ? 'Edit Data Barang' : 'Tambah Barang Baru' }}</span>
            </h3>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Kode Barang *</label>
                    <label class="input w-full font-mono bg-base-200/50 flex items-center">
                        <input type="text" wire:model="kode_barang" placeholder="B/000001" readonly class="grow cursor-not-allowed text-sm" />
                    </label>
                    @error('kode_barang')
                        <div class="text-error text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Kategori *</label>
                    <x-mary-choices-offline
                        wire:model="kategori"
                        :options="$kategoriOptions"
                        single
                        searchable
                        clearable
                        placeholder="-- Pilih Kategori --"
                    />
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Nama Barang / Tipe *</label>
                <x-mary-input wire:model="nama_barang" placeholder="Contoh: Laptop Dell Latitude 3420 Core i5" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Serial Number (SN)</label>
                    <x-mary-input wire:model="serial_number" placeholder="Contoh: CN-0R8568..." class="font-mono text-xs" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Status *</label>
                    <select wire:model="status" class="select select-bordered w-full select-sm">
                        @foreach($statusList as $st)
                            <option value="{{ $st }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Pemakai Saat Ini</label>
                <x-mary-choices-offline
                    wire:model="m_pemakai_id"
                    :options="$pemakaiOptions"
                    single
                    searchable
                    clearable
                    placeholder="-- Belum Ditugaskan / Di Gudang IT --"
                />
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Keterangan / Spesifikasi</label>
                <textarea wire:model="keterangan" class="textarea textarea-bordered w-full h-20" placeholder="Catatan tambahan..."></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md shadow-primary/20 w-full sm:w-auto">
                    Simpan Barang
                </button>
            </div>
        </form>
    </x-mary-modal>

    {{-- Modal Impor Excel --}}
    <x-mary-modal wire:model="showImportModal" class="backdrop-blur-sm" box-class="max-w-xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4">
            <h3 class="font-extrabold text-lg flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <x-mary-icon name="o-arrow-up-tray" class="w-5 h-5" />
                </div>
                <span>Impor Data Barang dari Excel</span>
            </h3>
        </div>

        <div class="space-y-4">
            {{-- Keamanan Data & Info Alert --}}
            <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs space-y-1.5 text-emerald-800 dark:text-emerald-300">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <x-mary-icon name="o-shield-check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                    <span>Jaminan Keamanan Data</span>
                </div>
                <p class="leading-relaxed opacity-90">
                    Data lama Anda <strong>100% aman</strong>. Fitur impor ini bersifat <em>menambahkan</em> (append-only) dan <strong>tidak akan menghapus data apapun</strong>.
                    Jika Serial Number atau Kode Barang di file Excel sudah ada di sistem, baris tersebut akan otomatis dilewati (skip).
                </p>
            </div>

            {{-- Download Template Card --}}
            <div class="p-3.5 rounded-xl bg-base-200/50 border border-base-content/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <div class="text-xs font-bold text-base-content">Belum punya format file?</div>
                    <div class="text-[11px] opacity-60">Unduh template resmi dengan petunjuk kolom dan contoh data</div>
                </div>
                <x-mary-button
                    label="Unduh Template Excel"
                    icon="o-arrow-down-tray"
                    wire:click="downloadTemplate"
                    class="btn-sm btn-outline btn-primary text-xs w-full sm:w-auto shrink-0"
                />
            </div>

            {{-- Upload Form --}}
            <form wire:submit="importExcel" class="space-y-4">
                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">
                        Pilih File Excel / CSV *
                    </label>
                    <input
                        type="file"
                        wire:model="importFile"
                        accept=".xlsx,.xls,.csv"
                        class="file-input file-input-bordered file-input-primary w-full file-input-sm text-xs"
                    />
                    <div class="flex items-center justify-between mt-1 text-[11px] opacity-60">
                        <span>Format didukung: .xlsx, .xls, .csv (Maks. 10MB)</span>
                        <span wire:loading wire:target="importFile" class="text-primary font-bold flex items-center gap-1">
                            <span class="loading loading-spinner loading-xs"></span> Mengunggah...
                        </span>
                    </div>
                    @error('importFile')
                        <div class="text-error text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Panduan Kolom --}}
                <div class="bg-base-200/40 p-3 rounded-xl border border-base-content/5 text-[11px] space-y-1 opacity-80">
                    <div class="font-bold text-xs opacity-90 mb-1">Kolom yang dikenali di Excel:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        <li><strong>Kode Barang</strong>: Opsional (Otomatis dibuatkan sistem jika kosong)</li>
                        <li><strong>Nama Barang</strong>: <span class="text-error font-semibold">Wajib</span></li>
                        <li><strong>Kategori</strong>: Opsional (komputer, laptop, ups, printer, monitor, dll.)</li>
                        <li><strong>Serial Number</strong>: Opsional (Dilewati jika sudah ada di sistem)</li>
                        <li><strong>Nama Pemakai</strong>: Opsional (Jika nama tidak ditemukan, masuk ke Gudang IT)</li>
                        <li><strong>Status</strong>: Opsional (aktif, sedang_service, rusak, dll. Default: aktif)</li>
                        <li><strong>Keterangan</strong>: Opsional (Spesifikasi atau catatan tambahan)</li>
                    </ul>
                </div>

                {{-- Hasil Ringkasan Impor --}}
                @if($importSummary)
                    <div class="p-3.5 rounded-xl bg-base-200/70 border border-base-content/10 space-y-2.5">
                        <div class="font-bold text-xs">Hasil Impor Terakhir:</div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-2 rounded-lg bg-base-100 border border-base-content/5">
                                <div class="text-lg font-black">{{ $importSummary['total'] }}</div>
                                <div class="opacity-60 text-[10px]">Total Baris</div>
                            </div>
                            <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                <div class="text-lg font-black">{{ $importSummary['success'] }}</div>
                                <div class="text-[10px] font-bold">Berhasil Ditambahkan</div>
                            </div>
                            <div class="p-2 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                <div class="text-lg font-black">{{ $importSummary['skipped'] }}</div>
                                <div class="text-[10px] font-bold">Dilewati (Duplikat/Kosong)</div>
                            </div>
                        </div>

                        @if(!empty($importSummary['skipped_details']))
                            <div class="mt-2 text-[11px]">
                                <div class="font-bold text-amber-600 dark:text-amber-400 mb-1">Rincian Baris yang Dilewati:</div>
                                <div class="max-h-28 overflow-y-auto space-y-1 bg-base-100 p-2 rounded-lg border border-base-content/5">
                                    @foreach($importSummary['skipped_details'] as $sd)
                                        <div class="flex items-start justify-between gap-2 border-b border-base-content/5 pb-1 last:border-0">
                                            <span class="font-mono text-[10px] opacity-70">Baris #{{ $sd['baris'] }}:</span>
                                            <span class="grow text-left text-amber-700 dark:text-amber-300">{{ $sd['alasan'] }}</span>
                                            <span class="opacity-50 text-[10px] truncate max-w-[120px]">{{ $sd['data'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-3 border-t border-base-content/10">
                    <button type="button" wire:click="closeImportModal" class="btn btn-ghost btn-sm w-full sm:w-auto">
                        Tutup
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary btn-sm px-6 shadow-md shadow-primary/20 w-full sm:w-auto gap-2"
                        wire:loading.attr="disabled"
                        wire:target="importExcel,importFile"
                    >
                        <span wire:loading.remove wire:target="importExcel">
                            <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4 inline-block mr-1" /> Mulai Impor Data
                        </span>
                        <span wire:loading wire:target="importExcel" class="flex items-center gap-1.5">
                            <span class="loading loading-spinner loading-xs"></span> Mengimpor...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </x-mary-modal>

    {{-- Modal Riwayat & Lifecycle Barang --}}
    <x-mary-modal wire:model="showHistoryModal" class="backdrop-blur-sm" box-class="max-w-4xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto" without-trap-focus>
        @if($historyData && $historyData['barang'])
            @php
                $hBarang = $historyData['barang'];
                $statusBadges = [
                    'aktif' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                    'tidak_aktif' => 'bg-base-200 text-base-content/60 border-base-content/10',
                    'sedang_service' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                    'rusak' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                    'dijual' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                ];
            @endphp

            {{-- Header Modal --}}
            <div class="flex items-start justify-between pb-4 border-b border-base-content/10 mb-4 gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-primary/10 text-primary shrink-0">
                        <x-mary-icon name="o-clock" class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-extrabold text-lg sm:text-xl text-base-content leading-tight">
                                {{ $hBarang->nama_barang }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                                {{ $hBarang->kode_barang }}
                            </span>
                        </div>
                        <p class="text-xs opacity-60 mt-0.5">
                            Kategori: <strong class="capitalize">{{ str_replace('_', ' ', $hBarang->kategori) }}</strong>
                            &bull; S/N: <strong class="font-mono">{{ $hBarang->serial_number ?: 'Tanpa S/N' }}</strong>
                        </p>
                    </div>
                </div>
                {{-- Hanya 1 tombol tutup (tanpa tombol X manual, sudah ada dari x-mary-modal) --}}
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                {{-- Status & Pemakai --}}
                <div class="p-3.5 rounded-xl bg-base-200/50 border border-base-content/5 space-y-1">
                    <div class="text-[10px] uppercase font-bold tracking-wider opacity-50">Posisi & Status Saat Ini</div>
                    <div class="text-sm font-extrabold text-base-content flex items-center gap-1.5 truncate">
                        <x-mary-icon name="o-user" class="w-4 h-4 text-primary shrink-0" />
                        <span class="truncate">{{ $hBarang->pemakai?->nama ?? 'Gudang / Tanpa Pemakai' }}</span>
                    </div>
                    <div class="text-xs opacity-60 truncate">
                        {{ $hBarang->pemakai?->department?->name ?? 'Belum teralokasi ke departemen' }}
                    </div>
                    <div class="pt-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $statusBadges[$hBarang->status] ?? 'bg-base-200' }}">
                            {{ ucfirst(str_replace('_', ' ', $hBarang->status)) }}
                        </span>
                    </div>
                </div>

                {{-- Asal Pengadaan / Pembelian --}}
                <div class="p-3.5 rounded-xl bg-base-200/50 border border-base-content/5 space-y-1">
                    <div class="text-[10px] uppercase font-bold tracking-wider opacity-50">Asal Pembelian / Pengadaan</div>
                    @if($historyData['pembelian'])
                        <div class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 truncate">
                            <x-mary-icon name="o-shopping-bag" class="w-4 h-4 shrink-0" />
                            <span class="truncate">{{ $historyData['pembelian']['details']['Supplier / Vendor'] }}</span>
                        </div>
                        <div class="text-xs text-base-content/80 font-mono font-bold">
                            {{ $historyData['pembelian']['details']['Harga Beli'] }}
                        </div>
                        <div class="text-[11px] opacity-70 flex items-center gap-1.5 flex-wrap">
                            <span>Tgl: {{ $historyData['pembelian']['date']->format('d/m/Y') }}</span>
                            <span>&bull;</span>
                            {{-- No. Mutasi sebagai link --}}
                            <a
                                href="{{ $historyData['pembelian']['link'] }}"
                                target="_blank"
                                class="inline-flex items-center gap-1 font-mono font-bold text-primary hover:underline hover:text-primary/80 transition-colors"
                                title="Buka halaman mutasi {{ $historyData['pembelian']['no_mutasi'] }}"
                            >
                                <x-mary-icon name="o-arrow-top-right-on-square" class="w-3 h-3" />
                                {{ $historyData['pembelian']['no_mutasi'] }}
                            </a>
                        </div>
                    @else
                        <div class="text-xs opacity-40 italic flex items-center gap-1 pt-2">
                            <x-mary-icon name="o-information-circle" class="w-4 h-4 shrink-0" />
                            <span>Belum ada mutasi pembelian</span>
                        </div>
                    @endif
                </div>

                {{-- Rekap Jejak --}}
                <div class="p-3.5 rounded-xl bg-base-200/50 border border-base-content/5 space-y-1">
                    <div class="text-[10px] uppercase font-bold tracking-wider opacity-50">Statistik Rekam Jejak</div>
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <span class="opacity-70">Total Perpindahan:</span>
                        <strong class="text-base-content">{{ $historyData['totalPerpindahan'] }} kali</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="opacity-70">Total Service:</span>
                        <strong class="text-base-content">{{ $historyData['totalService'] }} kali</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-1 border-t border-base-content/5">
                        <span class="opacity-70">Total Aktivitas:</span>
                        <strong class="text-primary">{{ $historyData['totalEvents'] }} peristiwa</strong>
                    </div>
                </div>
            </div>

            {{-- Tabs Filter --}}
            <div class="flex items-center justify-between gap-2 border-b border-base-content/10 pb-3 mb-4 flex-wrap">
                <div class="flex items-center gap-1.5 bg-base-200/70 p-1 rounded-xl text-xs font-semibold">
                    <button
                        type="button"
                        wire:click="$set('historyTab', 'all')"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $historyTab === 'all' ? 'bg-base-100 text-primary shadow-sm font-bold' : 'opacity-60 hover:opacity-100' }}"
                    >
                        Semua Riwayat ({{ $historyData['totalEvents'] }})
                    </button>
                    <button
                        type="button"
                        wire:click="$set('historyTab', 'mutasi')"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $historyTab === 'mutasi' ? 'bg-base-100 text-indigo-600 shadow-sm font-bold' : 'opacity-60 hover:opacity-100' }}"
                    >
                        Mutasi & Pembelian
                    </button>
                    <button
                        type="button"
                        wire:click="$set('historyTab', 'service')"
                        class="px-3 py-1.5 rounded-lg transition-all {{ $historyTab === 'service' ? 'bg-base-100 text-amber-600 shadow-sm font-bold' : 'opacity-60 hover:opacity-100' }}"
                    >
                        Riwayat Service
                    </button>
                </div>
                <div class="text-[11px] opacity-50 italic">
                    Diurutkan dari peristiwa terbaru
                </div>
            </div>

            {{-- Timeline Events --}}
            @if(count($historyData['events']) > 0)
                <div class="relative pl-6 sm:pl-8 space-y-4 before:absolute before:left-2.5 sm:before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-base-content/10 pb-2">
                    @foreach($historyData['events'] as $evt)
                        <div class="relative group">
                            <!-- Node Circle -->
                            <div class="absolute -left-6 sm:-left-8 top-1 w-5 sm:w-6 h-5 sm:h-6 rounded-full flex items-center justify-center border {{ $evt['icon_color'] }} shadow-sm">
                                <x-mary-icon name="{{ $evt['icon'] }}" class="w-3 sm:w-3.5 h-3 sm:h-3.5" />
                            </div>

                            <!-- Content Card -->
                            <div class="p-3.5 sm:p-4 rounded-xl bg-base-200/40 hover:bg-base-200/70 border border-base-content/5 transition-colors">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs sm:text-sm font-bold text-base-content">{{ $evt['title'] }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $evt['badge_class'] }}">
                                            {{ $evt['badge'] }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-mono font-medium opacity-60 flex items-center gap-1">
                                        <x-mary-icon name="o-calendar" class="w-3.5 h-3.5" />
                                        {{ $evt['date']->format('d M Y') }}
                                    </span>
                                </div>

                                @if(!empty($evt['subtitle']))
                                    <div class="text-xs font-mono font-semibold mb-2">
                                        @if(!empty($evt['link']))
                                            {{-- Subtitle jadi link untuk event mutasi --}}
                                            <a
                                                href="{{ $evt['link'] }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 text-primary/80 hover:text-primary hover:underline transition-colors"
                                                title="Buka detail mutasi {{ $evt['no_mutasi'] ?? '' }}"
                                            >
                                                <x-mary-icon name="o-arrow-top-right-on-square" class="w-3 h-3" />
                                                {{ $evt['subtitle'] }}
                                            </a>
                                        @else
                                            <span class="text-base-content/60">{{ $evt['subtitle'] }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-1.5 border-t border-base-content/5">
                                    @foreach($evt['details'] as $label => $val)
                                        <div class="flex flex-col">
                                            <span class="text-[10px] uppercase font-bold tracking-wider opacity-50">{{ $label }}</span>
                                            <span class="font-medium text-base-content">{{ $val }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-base-200 flex items-center justify-center text-base-content/30 mx-auto mb-2">
                        <x-mary-icon name="o-inbox" class="w-6 h-6" />
                    </div>
                    <div class="font-bold text-sm opacity-70">Belum Ada Rekam Jejak</div>
                    <p class="text-xs opacity-50 mt-1 max-w-sm mx-auto">
                        Barang ini belum memiliki catatan transaksi mutasi (pembelian/perpindahan) ataupun riwayat service pada filter yang dipilih.
                    </p>
                </div>
            @endif

            <div class="flex justify-end pt-4 border-t border-base-content/10 mt-4">
                <button type="button" wire:click="$set('showHistoryModal', false)" class="btn btn-ghost btn-sm rounded-xl">
                    Tutup
                </button>
            </div>
        @endif
    </x-mary-modal>
</div>
