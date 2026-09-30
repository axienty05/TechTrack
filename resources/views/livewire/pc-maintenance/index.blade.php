<div class="space-y-5">

    {{-- ======================================================= --}}
    {{-- HEADER & ACTIONS --}}
    {{-- ======================================================= --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-2xl bg-primary/10 text-primary border border-primary/20 shadow-sm flex items-center justify-center shrink-0">
                <x-mary-icon name="o-computer-desktop" class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">PC Maintenance</h1>
                <p class="text-xs sm:text-sm opacity-60">Jadwal & Riwayat Pemeliharaan Komputer PT. Aneka Coffee Industry</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button wire:click="exportExcel" class="btn btn-outline btn-success shadow-sm font-medium gap-2 flex-1 sm:flex-initial">
                <x-mary-icon name="o-arrow-down-tray" class="w-4 h-4" />
                <span>Export CSV</span>
            </button>
            <a href="{{ route('barangs') }}" class="btn btn-primary shadow-lg shadow-primary/20 font-medium gap-2 flex-1 sm:flex-initial">
                <x-mary-icon name="o-cube" class="w-4 h-4" />
                <span>Data Barang & Aset</span>
            </a>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- STATS CARDS --}}
    {{-- ======================================================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- User Kantor --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5 hover:border-primary/20 transition-all">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-building-office" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-primary">{{ $totalKantor }}</div>
                <div class="text-xs opacity-60 font-medium">User Kantor</div>
            </div>
        </div>

        {{-- User Pabrik --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5 hover:border-secondary/20 transition-all">
            <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary border border-secondary/20 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-building-storefront" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-secondary">{{ $totalPabrik }}</div>
                <div class="text-xs opacity-60 font-medium">User Pabrik</div>
            </div>
        </div>

        {{-- Selesai Periode Ini --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex items-center gap-3.5 hover:border-emerald-500/20 transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center shrink-0">
                <x-mary-icon name="o-check-circle" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight text-emerald-500">{{ $completedCurrentPeriod }}</div>
                <div class="text-xs opacity-60 font-medium">Selesai Periode Ini</div>
            </div>
        </div>

        {{-- Progres Periode --}}
        <div class="bg-base-100 p-4 rounded-2xl shadow-sm border border-base-content/5 flex flex-col justify-center gap-1.5 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-xs opacity-60 font-medium">Progres Periode</span>
                <span class="text-xs font-black text-primary">{{ $progressPercent }}%</span>
            </div>
            <progress class="progress progress-primary w-full h-2 rounded-full" value="{{ $progressPercent }}" max="100"></progress>
            <div class="text-[11px] opacity-50 truncate">{{ ucfirst($activeTab) }}: {{ $completedCurrentPeriod }} dari {{ $totalActiveTab }} selesai</div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- TABS & CONTROLS BAR --}}
    {{-- ======================================================= --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        {{-- Modern Tab Kantor vs Pabrik --}}
        <div class="inline-flex p-1 bg-base-200/80 rounded-2xl border border-base-content/5 gap-1 shadow-xs w-full sm:w-auto">
            <button type="button" wire:click="setActiveTab('kantor')"
                class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 cursor-pointer {{ $activeTab === 'kantor' ? 'bg-primary text-primary-content shadow-sm shadow-primary/20' : 'text-base-content/70 hover:text-base-content hover:bg-base-100/50' }}">
                <x-mary-icon name="o-building-office" class="w-4 h-4" />
                <span>Unit Kantor</span>
                <span class="badge badge-sm font-semibold {{ $activeTab === 'kantor' ? 'bg-primary-content/20 text-primary-content border-none' : 'badge-ghost' }}">
                    {{ $totalKantor }}
                </span>
            </button>
            <button type="button" wire:click="setActiveTab('pabrik')"
                class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 cursor-pointer {{ $activeTab === 'pabrik' ? 'bg-secondary text-secondary-content shadow-sm shadow-secondary/20' : 'text-base-content/70 hover:text-base-content hover:bg-base-100/50' }}">
                <x-mary-icon name="o-building-storefront" class="w-4 h-4" />
                <span>Unit Pabrik</span>
                <span class="badge badge-sm font-semibold {{ $activeTab === 'pabrik' ? 'bg-secondary-content/20 text-secondary-content border-none' : 'badge-ghost' }}">
                    {{ $totalPabrik }}
                </span>
            </button>
        </div>

        {{-- Periode Navigator --}}
        <div class="flex items-center justify-between sm:justify-end gap-2">
            @if($selectedPeriod !== date('Y-m'))
                <button type="button" wire:click="setPeriodToday"
                    class="btn btn-ghost btn-xs text-primary font-bold hover:bg-primary/10 transition-colors"
                    title="Kembali ke bulan ini">
                    Bulan Ini
                </button>
            @endif

            <div class="inline-flex items-center bg-base-100 p-1 rounded-2xl border border-base-content/10 shadow-xs">
                {{-- Tombol Bulan Sebelumnya --}}
                <button type="button" wire:click="prevPeriod"
                    class="btn btn-ghost btn-xs btn-square text-base-content/70 hover:text-base-content hover:bg-base-200/80 rounded-xl"
                    title="Bulan sebelumnya">
                    <x-mary-icon name="o-chevron-left" class="w-4 h-4" />
                </button>

                {{-- Display Bulan & Tahun (Klik untuk membuka picker) --}}
                <div class="relative flex items-center px-3 py-1 rounded-xl hover:bg-base-200/60 transition-colors cursor-pointer group">
                    <x-mary-icon name="o-calendar" class="w-4 h-4 text-primary mr-2 shrink-0 pointer-events-none group-hover:scale-110 transition-transform" />
                    <span class="text-xs font-extrabold text-base-content whitespace-nowrap pointer-events-none tracking-wide">
                        {{ $formattedPeriod }}
                    </span>
                    <input type="month" wire:model.live="selectedPeriod"
                        class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                        title="Klik untuk memilih bulan & tahun" />
                </div>

                {{-- Tombol Bulan Berikutnya --}}
                <button type="button" wire:click="nextPeriod"
                    class="btn btn-ghost btn-xs btn-square text-base-content/70 hover:text-base-content hover:bg-base-200/80 rounded-xl"
                    title="Bulan berikutnya">
                    <x-mary-icon name="o-chevron-right" class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- FILTER BAR CARD --}}
    {{-- ======================================================= --}}
    <div class="bg-base-100 p-3.5 sm:p-4 rounded-2xl shadow-sm border border-base-content/5 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
        {{-- Search & Filters Group --}}
        <div class="flex flex-wrap items-center gap-2.5 flex-1">
            {{-- Search Box --}}
            <div class="w-full sm:w-64">
                <x-mary-input wire:model.live.debounce.300ms="search"
                    placeholder="Cari user / comp name..."
                    icon="o-magnifying-glass"
                    clearable
                    class="input-sm" />
            </div>

            {{-- Department Filter --}}
            <div class="w-full sm:w-44">
                <select wire:model.live="departmentFilter" class="select select-bordered select-sm w-full font-medium">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="w-full sm:w-52">
                <select wire:model.live="statusFilter" class="select select-bordered select-sm w-full font-medium">
                    <option value="all">Semua Status</option>
                    <option value="completed">✅ Sudah Di-maintenance</option>
                    <option value="pending">⏳ Belum Di-maintenance</option>
                </select>
            </div>

            {{-- Toggle Excluded / Dikecualikan --}}
            <button type="button" wire:click="$toggle('showExcluded')"
                class="btn btn-sm gap-1.5 transition-all {{ $showExcluded ? 'btn-warning text-warning-content shadow-xs font-bold' : 'btn-ghost border border-base-content/10 text-base-content/70 hover:text-base-content hover:bg-base-200' }}"
                title="{{ $showExcluded ? 'Sembunyikan user yang dikecualikan' : 'Tampilkan user yang dikecualikan dari PC Maintenance' }}">
                <x-mary-icon name="{{ $showExcluded ? 'o-eye' : 'o-eye-slash' }}" class="w-4 h-4" />
                <span class="text-xs">Dikecualikan</span>
                @if($totalExcluded > 0)
                    <span class="badge badge-xs {{ $showExcluded ? 'bg-warning-content/25 text-warning-content border-none' : 'badge-warning font-bold' }}">
                        {{ $totalExcluded }}
                    </span>
                @endif
            </button>

            {{-- Reset Filter Button --}}
            @if($search || $departmentFilter || $statusFilter !== 'all' || $showExcluded)
                <button type="button" wire:click="resetFilters" class="btn btn-ghost btn-xs gap-1 text-xs text-error hover:bg-error/10" title="Reset filter">
                    <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" />
                    <span>Reset</span>
                </button>
            @endif
        </div>

        {{-- Counter Info --}}
        <div class="text-xs opacity-60 font-medium shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-base-content/5 text-right">
            <span>Menampilkan <strong class="text-base-content font-bold">{{ $pemakais->total() }}</strong> user</span>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MAIN TABLE --}}
    {{-- ======================================================= --}}
    <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50 text-xs uppercase tracking-wider text-base-content/70">
                    <tr>
                        <th class="w-12 text-center font-bold">#</th>
                        <th class="min-w-[160px] font-bold">User</th>
                        <th class="whitespace-nowrap min-w-[140px] font-bold">Comp Name</th>
                        <th class="font-bold">Dept</th>
                        <th class="whitespace-nowrap min-w-[130px] font-bold">Tanggal</th>
                        <th class="min-w-[180px] font-bold">Notes</th>
                        <th class="min-w-[120px] font-bold">Ttd User</th>
                        <th class="text-right font-bold min-w-[130px]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemakais as $pemakai)
                        @php
                            // Ambil barang komputer pertama milik pemakai ini
                            $barang    = $pemakai->barangs->first();
                            $record    = $barang?->pcMaintenances->first();
                            $isDone    = $record !== null;
                            $hasPC     = $barang !== null;
                            $isExcluded = $pemakai->exclude_pc_maintenance;
                        @endphp
                        <tr class="hover {{ $isDone ? '' : ($hasPC ? 'opacity-85' : 'opacity-60') }} {{ $isExcluded ? 'bg-warning/5' : '' }}">
                            <td class="text-xs opacity-50 font-mono text-center">{{ $pemakais->firstItem() + $loop->index }}</td>

                            {{-- User / Pemakai --}}
                            <td>
                                <div class="font-semibold text-sm {{ $isExcluded ? 'line-through opacity-40' : '' }}">{{ $pemakai->nama }}</div>
                                @if($isExcluded)
                                    <span class="badge badge-warning badge-xs gap-1 mt-0.5">
                                        <x-mary-icon name="o-minus-circle" class="w-2.5 h-2.5" />
                                        Dikecualikan
                                    </span>
                                @endif
                            </td>

                            {{-- Comp Name --}}
                            <td class="whitespace-nowrap">
                                @if($hasPC)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-base-200/80 border border-base-content/10 font-mono text-xs font-extrabold tracking-wide text-primary whitespace-nowrap shadow-xs">
                                        <x-mary-icon name="o-computer-desktop" class="w-3.5 h-3.5 text-primary shrink-0" />
                                        <span>{{ $pemakai->comp_name ?: $barang->nama_barang }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs opacity-40 italic">
                                        <x-mary-icon name="o-computer-desktop" class="w-3.5 h-3.5 shrink-0" />
                                        Belum ada komputer
                                    </span>
                                @endif
                            </td>

                            {{-- Dept --}}
                            <td>
                                @if($pemakai->department)
                                    <span class="badge badge-ghost badge-sm font-semibold">{{ $pemakai->department->code }}</span>
                                @else
                                    <span class="text-xs opacity-30">-</span>
                                @endif
                            </td>

                            {{-- Tanggal Maintenance --}}
                            <td>
                                @if($isDone)
                                    <div class="text-sm font-semibold text-success flex items-center gap-1">
                                        <x-mary-icon name="o-check-circle" class="w-4 h-4 shrink-0 text-success" />
                                        <span>{{ $record->maintenance_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="text-[11px] opacity-50">{{ $record->technician->name ?? 'Teknisi IT' }}</div>
                                @elseif($hasPC)
                                    <span class="badge badge-warning badge-sm gap-1">
                                        <x-mary-icon name="o-clock" class="w-3 h-3" />
                                        Belum
                                    </span>
                                @else
                                    <span class="text-xs opacity-30">-</span>
                                @endif
                            </td>

                            {{-- Notes (Kondisi + Catatan) --}}
                            <td class="max-w-xs">
                                @if($isDone)
                                    @php
                                        $condColors = [
                                            'good'             => 'badge-success',
                                            'needs_attention'  => 'badge-warning',
                                            'critical'         => 'badge-error',
                                        ];
                                        $condLabels = [
                                            'good'             => 'Baik',
                                            'needs_attention'  => 'Perlu Perhatian',
                                            'critical'         => 'Kritis',
                                        ];
                                        $condColor = $condColors[$record->overall_condition] ?? 'badge-ghost';
                                        $condLabel = $condLabels[$record->overall_condition] ?? $record->overall_condition;
                                    @endphp
                                    <span class="badge {{ $condColor }} badge-sm mb-0.5">{{ $condLabel }}</span>
                                    @if($record->notes)
                                        <div class="text-xs opacity-60 line-clamp-2">{{ $record->notes }}</div>
                                    @endif
                                @else
                                    <span class="text-xs opacity-30">-</span>
                                @endif
                            </td>

                            {{-- Ttd User --}}
                            <td>
                                @if($isDone && $record->is_user_signed)
                                    <div class="flex items-center gap-1 text-success text-xs font-semibold">
                                        <x-mary-icon name="o-check-badge" class="w-4 h-4 shrink-0" />
                                        <span>{{ $record->user_sign_name ?: 'Sudah TTD' }}</span>
                                    </div>
                                @elseif($isDone)
                                    <span class="text-xs opacity-40">Belum TTD</span>
                                @else
                                    <span class="text-xs opacity-30">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($isExcluded)
                                        {{-- Restore button --}}
                                        <button wire:click="restoreToMaintenance({{ $pemakai->id }})"
                                            wire:confirm="Munculkan kembali {{ $pemakai->nama }} di PC Maintenance?"
                                            class="btn btn-outline btn-warning btn-sm gap-1"
                                            title="Munculkan kembali">
                                            <x-mary-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                                            <span class="hidden sm:inline">Restore</span>
                                        </button>
                                    @elseif($hasPC)
                                        {{-- Maintenance Checklist Button --}}
                                        <button wire:click="openMaintenanceModal({{ $barang->id }})"
                                            class="btn btn-sm gap-1 {{ $isDone ? 'btn-success btn-outline' : 'btn-primary' }}"
                                            title="{{ $isDone ? 'Edit Maintenance' : 'Catat Maintenance' }}">
                                            <x-mary-icon name="{{ $isDone ? 'o-pencil-square' : 'o-clipboard-document-check' }}" class="w-4 h-4" />
                                            <span class="hidden sm:inline">{{ $isDone ? 'Edit' : 'Catat' }}</span>
                                        </button>
                                        {{-- History Button --}}
                                        <button wire:click="openHistoryModal({{ $barang->id }})"
                                            class="btn btn-ghost btn-sm btn-square"
                                            title="Riwayat Pemeliharaan PC">
                                            <x-mary-icon name="o-clock" class="w-4 h-4 text-info" />
                                        </button>
                                    @else
                                        {{-- No PC yet: link to add barang --}}
                                        <a href="{{ route('barangs') }}"
                                            class="btn btn-ghost btn-sm gap-1 opacity-60"
                                            title="Tambah komputer untuk user ini">
                                            <x-mary-icon name="o-plus-circle" class="w-4 h-4" />
                                            <span class="hidden sm:inline text-xs">Tambah PC</span>
                                        </a>
                                    @endif

                                    {{-- Tombol Hapus dari PC Maintenance (hanya muncul jika belum di-exclude) --}}
                                    @if(!$isExcluded)
                                        <button wire:click="excludeFromMaintenance({{ $pemakai->id }})"
                                            wire:confirm="Hapus {{ $pemakai->nama }} dari daftar PC Maintenance? (User tetap ada di data Pemakai, bisa dikembalikan kapan saja)"
                                            class="btn btn-ghost btn-sm btn-square text-base-content/40 hover:text-error hover:bg-error/10 transition-colors"
                                            title="Hapus dari PC Maintenance">
                                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center gap-2 opacity-50">
                                    <x-mary-icon name="o-users" class="w-12 h-12 text-base-content/40" />
                                    <span class="font-semibold text-sm">Tidak ada user yang sesuai dengan filter</span>
                                    <p class="text-xs opacity-60">Pastikan data Pemakai sudah terdaftar di departemen yang sesuai.</p>
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

    {{-- ======================================================= --}}
    {{-- MODAL: INPUT / EDIT MAINTENANCE --}}
    {{-- ======================================================= --}}
    <x-mary-modal wire:model="showMaintenanceModal" class="backdrop-blur-sm" box-class="max-w-2xl w-full max-h-[92vh] overflow-y-auto !p-0">
        {{-- Modal Header --}}
        <div class="sticky top-0 z-10 bg-base-100 border-b border-base-content/10 px-5 py-4 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <div class="bg-primary/10 rounded-lg p-1.5 text-primary">
                        <x-mary-icon name="o-clipboard-document-check" class="w-5 h-5" />
                    </div>
                    <span>Form Maintenance PC</span>
                </h3>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-primary/10 border border-primary/20 font-mono text-xs font-bold text-primary">
                        <x-mary-icon name="o-computer-desktop" class="w-3.5 h-3.5" />
                        {{ $maintCompName }}
                    </span>
                    <span class="text-xs opacity-75 font-medium">{{ $maintNamaBarang }}</span>
                    <span class="text-xs opacity-50">• Pemakai: {{ $maintUserName }}</span>
                    @if($maintHasExisting)
                        <span class="badge badge-success badge-sm gap-1 ml-auto">
                            <x-mary-icon name="o-check-circle" class="w-3 h-3" />
                            Sudah diisi
                        </span>
                    @else
                        <span class="badge badge-warning badge-sm gap-1 ml-auto">
                            <x-mary-icon name="o-clock" class="w-3 h-3" />
                            Belum diisi
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <form wire:submit="saveMaintenance" class="px-5 py-4 space-y-5">
            {{-- Info Work Log otomatis --}}
            <div class="p-3 rounded-xl bg-primary/5 border border-primary/20 text-xs text-primary flex items-center gap-2.5">
                <x-mary-icon name="o-information-circle" class="w-5 h-5 shrink-0" />
                <span>Hasil checklist maintenance ini akan <strong>otomatis tercatat di Work Log harian IT</strong>.</span>
            </div>

            {{-- Section: Tanggal & Periode --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider opacity-60 mb-3 flex items-center gap-2">
                    <x-mary-icon name="o-calendar-days" class="w-4 h-4 text-primary" />
                    Jadwal Pelaksanaan
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label py-1"><span class="label-text text-xs font-semibold">Tanggal Pelaksanaan *</span></label>
                        <input type="date" wire:model="maintDate" class="input input-bordered w-full input-sm focus:input-primary font-mono" />
                    </div>
                    <div>
                        <label class="label py-1"><span class="label-text text-xs font-semibold">Periode (Bulan) *</span></label>
                        <input type="month" wire:model="maintPeriod" class="input input-bordered w-full input-sm focus:input-primary font-mono" />
                    </div>
                </div>
            </div>

            <div class="divider my-0"></div>

            {{-- Section: Checklist Standar --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider opacity-60 mb-3 flex items-center gap-2">
                    <x-mary-icon name="o-clipboard-document-list" class="w-4 h-4 text-primary" />
                    Checklist Pemeriksaan Standar
                </h4>
                <div class="bg-base-200/40 rounded-xl p-4 grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-4">
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-base-300/50 rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" wire:model="checklist.clean_dust" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Pembersihan debu & casing fisik</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-base-300/50 rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" wire:model="checklist.check_thermal" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Pengecekan / ganti pasta thermal</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-base-300/50 rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" wire:model="checklist.antivirus_scan" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Scan & update antivirus</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-base-300/50 rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" wire:model="checklist.disk_cleanup" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Disk cleanup & pembersihan temp files</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-base-300/50 rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" wire:model="checklist.os_update" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Update OS / Windows Update</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-base-300/50 rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" wire:model="checklist.network_test" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Tes koneksi jaringan LAN / WiFi</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-base-300/50 rounded-lg px-2 py-1.5 transition-colors sm:col-span-2">
                        <input type="checkbox" wire:model="checklist.backup_data" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Pengecekan / backup data penting user</span>
                    </label>
                </div>
            </div>

            {{-- Section: Kondisi & Notes --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider opacity-60 mb-3 flex items-center gap-2">
                    <x-mary-icon name="o-document-text" class="w-4 h-4 text-primary" />
                    Kondisi & Catatan Tindakan
                </h4>
                <div class="bg-base-200/40 rounded-xl p-4 space-y-3">
                    <div>
                        <label class="label py-0.5"><span class="label-text text-xs font-semibold">Kondisi Keseluruhan PC</span></label>
                        <select wire:model="maintCondition" class="select select-bordered w-full select-sm focus:select-primary">
                            <option value="good">✅ Baik (Normal)</option>
                            <option value="needs_attention">⚠️ Perlu Perhatian (Ada Catatan)</option>
                            <option value="critical">🔴 Kritis (Perlu Tindakan Lanjut)</option>
                        </select>
                    </div>
                    <div>
                        <label class="label py-0.5"><span class="label-text text-xs font-semibold">Catatan / Temuan Teknisi</span></label>
                        <textarea wire:model="maintNotes" rows="2"
                            class="textarea textarea-bordered w-full text-sm focus:textarea-primary leading-relaxed"
                            placeholder="Catatan tambahan teknisi..."></textarea>
                    </div>
                </div>
            </div>

            <div class="divider my-0"></div>

            {{-- Section: Verifikasi Pemakai --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider opacity-60 mb-3 flex items-center gap-2">
                    <x-mary-icon name="o-user-circle" class="w-4 h-4 text-primary" />
                    Verifikasi Pemakai
                </h4>
                <div class="bg-base-200/40 rounded-xl p-4 space-y-3">
                    <div>
                        <label class="label py-0.5"><span class="label-text text-xs font-semibold">Nama Pemakai / PIC</span></label>
                        <input type="text" wire:model="maintUserSignName"
                            class="input input-bordered w-full input-sm focus:input-primary"
                            placeholder="Nama user yang menerima maintenance" />
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer bg-base-300/30 hover:bg-base-300/60 rounded-lg px-3 py-2.5 transition-colors">
                        <input type="checkbox" wire:model="maintIsUserSigned" class="checkbox checkbox-primary checkbox-sm" />
                        <span class="text-sm font-medium">Sudah konfirmasi / tanda tangan (TTD) pemakai</span>
                    </label>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="sticky bottom-0 bg-base-100 border-t border-base-content/10 pt-4 pb-1 -mx-5 px-5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    @if($maintHasExisting)
                        <button type="button"
                            wire:click="deleteCurrentMaintenance"
                            wire:confirm="Reset status maintenance {{ $maintCompName }} untuk periode ini? Data checklist dan Work Log terkait akan dihapus."
                            class="btn btn-outline btn-warning btn-sm gap-2 w-full sm:w-auto">
                            <x-mary-icon name="o-arrow-path" class="w-4 h-4" />
                            Reset Maintenance
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="button" wire:click="$set('showMaintenanceModal', false)" class="btn btn-ghost btn-sm flex-1 sm:flex-initial">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-8 shadow-md flex-1 sm:flex-initial gap-2">
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        Simpan & Masuk Work Log
                    </button>
                </div>
            </div>
        </form>
    </x-mary-modal>

    {{-- ======================================================= --}}
    {{-- MODAL: PC HEALTH HISTORY --}}
    {{-- ======================================================= --}}
    <x-mary-modal wire:model="showHistoryModal" class="backdrop-blur-sm" box-class="max-w-2xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        @if($historyBarang)
            <h3 class="font-bold text-base sm:text-lg mb-1 flex items-center gap-2">
                <x-mary-icon name="o-clock" class="text-info w-6 h-6 flex-shrink-0" />
                <span>Riwayat Pemeliharaan PC (Health History)</span>
            </h3>
            <div class="flex items-center gap-3 mb-4 bg-base-200/50 rounded-xl p-3 border border-base-content/5">
                <div class="bg-primary/10 rounded-lg p-2 flex-shrink-0 text-primary">
                    <x-mary-icon name="o-computer-desktop" class="w-6 h-6" />
                </div>
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-base-300/70 font-bold font-mono text-sm tracking-wide text-primary border border-base-content/10 truncate">
                        <span>{{ $historyBarang->pemakai?->comp_name ?: $historyBarang->nama_barang }}</span>
                    </div>
                    <div class="text-xs sm:text-sm font-medium mt-1 truncate">{{ $historyBarang->nama_barang }}</div>
                    <div class="text-xs opacity-60 mt-0.5 truncate">
                        Pemakai: {{ $historyBarang->pemakai?->nama ?: 'Tanpa Pemakai' }}
                        @if($historyBarang->pemakai?->department)
                            — Divisi {{ $historyBarang->pemakai->department->name }} ({{ $historyBarang->pemakai->department->code }})
                        @endif
                    </div>
                </div>
            </div>

            @if($historyBarang->pcMaintenances->isEmpty())
                <div class="text-center py-8 opacity-40">
                    <x-mary-icon name="o-clipboard-document" class="w-12 h-12 mx-auto mb-2" />
                    <p class="text-sm">Belum ada riwayat maintenance untuk perangkat ini</p>
                </div>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @foreach($historyBarang->pcMaintenances as $rec)
                        @php
                            $condBadge = match($rec->overall_condition) {
                                'critical'        => 'badge-error',
                                'needs_attention' => 'badge-warning',
                                default           => 'badge-success',
                            };
                            $condText = match($rec->overall_condition) {
                                'critical'        => 'Kritis',
                                'needs_attention' => 'Perlu Perhatian',
                                default           => 'Baik',
                            };
                        @endphp
                        <div class="bg-base-100 border border-base-content/10 rounded-xl p-3.5 space-y-2 shadow-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm">{{ $rec->maintenance_date->format('d M Y') }}</span>
                                    <span class="badge badge-outline badge-xs font-mono">{{ $rec->period }}</span>
                                    <span class="badge {{ $condBadge }} badge-xs">{{ $condText }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs opacity-50">{{ $rec->technician->name ?? 'Teknisi' }}</span>
                                    <button wire:click="deleteRecord({{ $rec->id }})" wire:confirm="Hapus catatan riwayat tanggal {{ $rec->maintenance_date->format('d/m/Y') }}?" class="btn btn-ghost btn-circle btn-xs text-error" title="Hapus catatan">
                                        <x-mary-icon name="o-trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>

                            @if(is_array($rec->checklist_items) && count($rec->checklist_items) > 0)
                                @php
                                    $labels = [
                                        'clean_dust'     => 'Debu Fisik',
                                        'check_thermal'  => 'Pasta Termal',
                                        'antivirus_scan' => 'Antivirus',
                                        'disk_cleanup'   => 'Disk Cleanup',
                                        'os_update'      => 'Update OS',
                                        'network_test'   => 'Jaringan',
                                        'backup_data'    => 'Backup Data',
                                    ];
                                @endphp
                                <div class="flex flex-wrap gap-1 text-[11px]">
                                    @foreach($labels as $key => $lbl)
                                        @if(!empty($rec->checklist_items[$key]))
                                            <span class="badge badge-sm badge-success/15 text-success gap-1 border-0">
                                                <x-mary-icon name="o-check" class="w-3 h-3" />
                                                {{ $lbl }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if($rec->notes)
                                <div class="text-xs bg-base-200/50 rounded-lg p-2 opacity-80 whitespace-pre-wrap">
                                    {{ $rec->notes }}
                                </div>
                            @endif

                            <div class="text-[11px] opacity-60 flex items-center justify-between pt-1 border-t border-base-content/5">
                                <span>Verifikasi: {{ $rec->user_sign_name ?: '-' }}</span>
                                <span>{{ $rec->is_user_signed ? '✅ Sudah TTD' : '⏳ Belum TTD' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end mt-4 pt-3 border-t border-base-content/10">
                <button type="button" wire:click="$set('showHistoryModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Tutup</button>
            </div>
        @endif
    </x-mary-modal>

</div>
