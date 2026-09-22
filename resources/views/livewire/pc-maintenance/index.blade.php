<div class="space-y-4 sm:space-y-5">

    {{-- ======================================================= --}}
    {{-- HEADER & STATS --}}
    {{-- ======================================================= --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight flex items-center gap-2">
                <x-mary-icon name="o-computer-desktop" class="w-7 h-7 sm:w-8 sm:h-8 text-primary flex-shrink-0" />
                <span>PC Maintenance</span>
            </h1>
            <p class="text-xs sm:text-sm opacity-60 mt-0.5">Jadwal Pemeliharaan Komputer PT. Aneka Coffee Industry</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button wire:click="openImportModal" class="btn btn-outline btn-sm gap-2 flex-1 sm:flex-initial">
                <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4" />
                Import Data
            </button>
            <button wire:click="openCreateDeviceModal" class="btn btn-primary btn-sm gap-2 shadow-lg flex-1 sm:flex-initial">
                <x-mary-icon name="o-plus" class="w-4 h-4" />
                Tambah PC
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 p-3 sm:p-4 flex items-center gap-2.5 sm:gap-3">
            <div class="bg-primary/10 rounded-xl p-2 sm:p-3 flex-shrink-0">
                <x-mary-icon name="o-building-office" class="w-5 h-5 sm:w-6 sm:h-6 text-primary" />
            </div>
            <div class="min-w-0">
                <div class="text-xl sm:text-2xl font-bold text-primary truncate">{{ $totalKantor }}</div>
                <div class="text-[11px] sm:text-xs opacity-60 truncate">Unit Kantor</div>
            </div>
        </div>
        <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 p-3 sm:p-4 flex items-center gap-2.5 sm:gap-3">
            <div class="bg-secondary/10 rounded-xl p-2 sm:p-3 flex-shrink-0">
                <x-mary-icon name="o-building-storefront" class="w-5 h-5 sm:w-6 sm:h-6 text-secondary" />
            </div>
            <div class="min-w-0">
                <div class="text-xl sm:text-2xl font-bold text-secondary truncate">{{ $totalPabrik }}</div>
                <div class="text-[11px] sm:text-xs opacity-60 truncate">Unit Pabrik</div>
            </div>
        </div>
        <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 p-3 sm:p-4 flex items-center gap-2.5 sm:gap-3">
            <div class="bg-success/10 rounded-xl p-2 sm:p-3 flex-shrink-0">
                <x-mary-icon name="o-check-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-success" />
            </div>
            <div class="min-w-0">
                <div class="text-xl sm:text-2xl font-bold text-success truncate">{{ $completedCurrentPeriod }}</div>
                <div class="text-[11px] sm:text-xs opacity-60 truncate">Selesai Periode Ini</div>
            </div>
        </div>
        <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 p-3 sm:p-4 flex flex-col justify-center gap-1 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs opacity-60">Progres Periode</span>
                <span class="text-xs font-bold text-primary">{{ $progressPercent }}%</span>
            </div>
            <progress class="progress progress-primary w-full h-2" value="{{ $progressPercent }}" max="100"></progress>
            <div class="text-[11px] sm:text-xs opacity-50 truncate">{{ ucfirst($activeTab) }}: {{ $completedCurrentPeriod }}/{{ $totalActiveTab }} selesai</div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- FILTER BAR --}}
    {{-- ======================================================= --}}
    <div class="bg-base-100 rounded-2xl shadow-sm border border-base-content/5 p-3 sm:p-4 space-y-3">
        {{-- Tab Kantor / Pabrik --}}
        <div class="tabs tabs-boxed bg-base-200/60 w-full sm:w-fit grid grid-cols-2 sm:flex">
            <button wire:click="$set('activeTab','kantor')" class="tab gap-2 {{ $activeTab === 'kantor' ? 'tab-active' : '' }}">
                <x-mary-icon name="o-building-office" class="w-4 h-4" />
                <span>Unit Kantor ({{ $totalKantor }})</span>
            </button>
            <button wire:click="$set('activeTab','pabrik')" class="tab gap-2 {{ $activeTab === 'pabrik' ? 'tab-active' : '' }}">
                <x-mary-icon name="o-building-storefront" class="w-4 h-4" />
                <span>Unit Pabrik ({{ $totalPabrik }})</span>
            </button>
        </div>

        {{-- Filter Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3 items-center">
            {{-- Search --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <x-mary-input wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama user / comp name..."
                    icon="o-magnifying-glass" clearable />
            </div>
            {{-- Dept Filter --}}
            <div>
                <select wire:model.live="departmentFilter" class="select select-bordered select-sm w-full">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
            </div>
            {{-- Status Filter --}}
            <div>
                <select wire:model.live="statusFilter" class="select select-bordered select-sm w-full">
                    <option value="all">Semua Status</option>
                    <option value="completed">✅ Sudah Di-maintenance</option>
                    <option value="pending">⏳ Belum Di-maintenance</option>
                </select>
            </div>
            {{-- Periode --}}
            <div>
                <input type="month" wire:model.live="selectedPeriod"
                    class="input input-bordered input-sm w-full"
                    title="Pilih Periode" />
            </div>
            {{-- Export --}}
            <div>
                <button wire:click="exportExcel" class="btn btn-success btn-sm gap-2 w-full">
                    <x-mary-icon name="o-arrow-down-tray" class="w-4 h-4" />
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MAIN TABLE --}}
    {{-- ======================================================= --}}
    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="w-12">#</th>
                        <th>User</th>
                        <th class="whitespace-nowrap min-w-[140px]">Comp Name</th>
                        <th>Dept</th>
                        <th>Tanggal Maintenance</th>
                        <th>Kondisi & Notes</th>
                        <th>Ttd User</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        @php
                            $record = $device->maintenanceRecords->first();
                            $isDone = $record !== null;
                        @endphp
                        <tr class="hover {{ $isDone ? '' : 'opacity-80' }}">
                            <td class="text-xs opacity-50 font-mono">{{ $devices->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="font-semibold text-sm">{{ $device->user_name }}</div>
                                <div class="text-xs opacity-50">{{ $device->device_type }}</div>
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-base-200/80 border border-base-content/10 font-mono text-xs font-semibold tracking-wide text-base-content whitespace-nowrap shadow-xs">
                                    <x-mary-icon name="o-computer-desktop" class="w-3.5 h-3.5 text-primary/80 shrink-0" />
                                    <span>{{ $device->comp_name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($device->department)
                                    <span class="badge badge-ghost badge-sm">{{ $device->department->code }}</span>
                                @else
                                    <span class="text-xs opacity-30">-</span>
                                @endif
                            </td>
                            <td>
                                @if($isDone)
                                    <div class="text-sm font-semibold text-success">
                                        {{ $record->maintenance_date->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs opacity-50">{{ $record->technician->name ?? 'Unknown' }}</div>
                                @else
                                    <span class="badge badge-warning badge-sm gap-1">
                                        <x-mary-icon name="o-clock" class="w-3 h-3" />
                                        Belum
                                    </span>
                                @endif
                            </td>
                            <td class="max-w-xs">
                                @if($isDone)
                                    @php
                                        $condColors = [
                                            'good' => 'badge-success',
                                            'needs_attention' => 'badge-warning',
                                            'critical' => 'badge-error',
                                        ];
                                        $condLabels = [
                                            'good' => 'Baik',
                                            'needs_attention' => 'Perlu Perhatian',
                                            'critical' => 'Kritis',
                                        ];
                                        $condColor = $condColors[$record->overall_condition] ?? 'badge-ghost';
                                        $condLabel = $condLabels[$record->overall_condition] ?? $record->overall_condition;
                                    @endphp
                                    <span class="badge {{ $condColor }} badge-sm mb-1">{{ $condLabel }}</span>
                                    @if($record->notes)
                                        <div class="text-xs opacity-60 line-clamp-2">{{ $record->notes }}</div>
                                    @endif
                                @else
                                    <span class="text-xs opacity-30">-</span>
                                @endif
                            </td>
                            <td>
                                @if($isDone && $record->is_user_signed)
                                    <div class="flex items-center gap-1 text-success text-xs font-semibold">
                                        <x-mary-icon name="o-check-badge" class="w-4 h-4" />
                                        {{ $record->user_sign_name ?: 'Sudah TTD' }}
                                    </div>
                                @elseif($isDone)
                                    <span class="text-xs opacity-40">Belum TTD</span>
                                @else
                                    <span class="text-xs opacity-30">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Maintenance Checklist Button --}}
                                    <button wire:click="openMaintenanceModal({{ $device->id }})"
                                        class="btn btn-sm gap-1 {{ $isDone ? 'btn-success btn-outline' : 'btn-primary' }}"
                                        title="{{ $isDone ? 'Edit Maintenance' : 'Input Maintenance' }}">
                                        <x-mary-icon name="{{ $isDone ? 'o-pencil-square' : 'o-clipboard-document-check' }}" class="w-4 h-4" />
                                        <span class="hidden sm:inline">{{ $isDone ? 'Edit' : 'Input' }}</span>
                                    </button>
                                    {{-- History Button --}}
                                    <button wire:click="openHistoryModal({{ $device->id }})"
                                        class="btn btn-ghost btn-sm btn-square"
                                        title="Riwayat PC">
                                        <x-mary-icon name="o-clock" class="w-4 h-4 text-info" />
                                    </button>
                                    {{-- Edit PC Button --}}
                                    <button wire:click="openEditDeviceModal({{ $device->id }})"
                                        class="btn btn-ghost btn-sm btn-square"
                                        title="Edit Data PC">
                                        <x-mary-icon name="o-cog-6-tooth" class="w-4 h-4 text-warning" />
                                    </button>
                                    {{-- Delete --}}
                                    <button wire:click="deleteDevice({{ $device->id }})"
                                        wire:confirm="Hapus perangkat {{ $device->comp_name }} ({{ $device->user_name }})? Semua riwayat maintenance juga akan dihapus."
                                        class="btn btn-ghost btn-sm btn-square text-error"
                                        title="Hapus">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center gap-2 opacity-40">
                                    <x-mary-icon name="o-computer-desktop" class="w-12 h-12" />
                                    <span>Tidak ada data perangkat ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $devices->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- MODAL: INPUT / EDIT MAINTENANCE -->
    <!-- ======================================================= -->
    <x-mary-modal wire:model="showMaintenanceModal" class="backdrop-blur-sm" box-class="max-w-2xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <h3 class="font-bold text-base sm:text-lg mb-1 flex items-center gap-2">
            <x-mary-icon name="o-clipboard-document-check" class="text-primary w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" />
            <span>Form Maintenance PC</span>
        </h3>
        <div class="flex items-center gap-2 mb-4 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-base-200 border border-base-content/10 font-mono text-xs font-bold text-primary">
                <x-mary-icon name="o-computer-desktop" class="w-3.5 h-3.5 text-primary" />
                {{ $maintCompName }}
            </span>
            <span class="text-xs sm:text-sm opacity-70">— {{ $maintUserName }}</span>
        </div>

        <form wire:submit="saveMaintenance" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Tanggal Pelaksanaan *</label>
                    <input type="date" wire:model="maintDate" class="input input-bordered w-full input-sm" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Periode (YYYY-MM) *</label>
                    <input type="month" wire:model="maintPeriod" class="input input-bordered w-full input-sm" />
                </div>
            </div>

            {{-- Checklist --}}
            <div>
                <label class="label text-xs font-bold uppercase opacity-70 mb-2">Checklist Pemeriksaan Standar</label>
                <div class="bg-base-200/50 rounded-xl p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="checklist.clean_dust" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Pembersihan debu & casing</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="checklist.check_thermal" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Pengecekan & ganti pasta thermal</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="checklist.antivirus_scan" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Scan & update antivirus</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="checklist.disk_cleanup" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Disk cleanup & hapus temp file</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="checklist.os_update" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Update OS / Windows Update</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="checklist.network_test" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Tes koneksi jaringan</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="checklist.backup_data" class="checkbox checkbox-success checkbox-sm" />
                        <span class="text-sm">Pengecekan/backup data penting</span>
                    </label>
                </div>
            </div>

            {{-- Kondisi & Notes --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-1">
                    <label class="label text-xs font-bold uppercase opacity-70">Kondisi PC</label>
                    <select wire:model="maintCondition" class="select select-bordered w-full select-sm">
                        <option value="good">✅ Baik</option>
                        <option value="needs_attention">⚠️ Perlu Perhatian</option>
                        <option value="critical">🔴 Kritis</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="label text-xs font-bold uppercase opacity-70">Catatan / Temuan</label>
                    <textarea wire:model="maintNotes" rows="2"
                        class="textarea textarea-bordered w-full text-sm"
                        placeholder="Debu banyak, antivirus diperbarui, dll..."></textarea>
                </div>
            </div>

            {{-- User Sign --}}
            <div class="bg-base-200/50 rounded-xl p-3 sm:p-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                <div class="flex-1 w-full sm:w-auto">
                    <label class="label text-xs font-bold uppercase opacity-70">Nama Pemakai / PIC (Verifikasi)</label>
                    <input type="text" wire:model="maintUserSignName"
                        class="input input-bordered w-full input-sm"
                        placeholder="Nama user yang menerima maintenance" />
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer gap-2 mt-1 sm:mt-6">
                        <input type="checkbox" wire:model="maintIsUserSigned" class="checkbox checkbox-primary checkbox-sm" />
                        <span class="label-text text-sm font-medium">Sudah konfirmasi TTD</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showMaintenanceModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md w-full sm:w-auto">
                    <x-mary-icon name="o-check" class="w-4 h-4" />
                    Simpan
                </button>
            </div>
        </form>
    </x-mary-modal>

    <!-- ======================================================= -->
    <!-- MODAL: HISTORY / PC HEALTH HISTORY -->
    <!-- ======================================================= -->
    <x-mary-modal wire:model="showHistoryModal" class="backdrop-blur-sm" box-class="max-w-2xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        @if($historyDevice)
            <h3 class="font-bold text-base sm:text-lg mb-1 flex items-center gap-2">
                <x-mary-icon name="o-clock" class="text-info w-6 h-6 flex-shrink-0" />
                <span>PC Health History</span>
            </h3>
            <div class="flex items-center gap-3 mb-4 bg-base-200/50 rounded-xl p-3">
                <div class="bg-primary/10 rounded-lg p-2 flex-shrink-0">
                    <x-mary-icon name="o-computer-desktop" class="w-6 h-6 text-primary" />
                </div>
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-base-300/70 font-bold font-mono text-sm tracking-wide text-base-content border border-base-content/10 truncate">
                        <x-mary-icon name="o-computer-desktop" class="w-4 h-4 text-primary flex-shrink-0" />
                        <span class="truncate">{{ $historyDevice->comp_name }}</span>
                    </div>
                    <div class="text-xs sm:text-sm opacity-70 mt-1 truncate">{{ $historyDevice->user_name }} — {{ $historyDevice->department->name ?? '-' }}</div>
                    <span class="badge badge-sm badge-ghost capitalize mt-1">{{ $historyDevice->location }}</span>
                </div>
            </div>

            @if($historyDevice->maintenanceRecords->isEmpty())
                <div class="text-center py-8 opacity-40">
                    <x-mary-icon name="o-clipboard-document" class="w-12 h-12 mx-auto mb-2" />
                    <p class="text-sm">Belum ada riwayat maintenance untuk perangkat ini</p>
                </div>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @foreach($historyDevice->maintenanceRecords as $rec)
                        @php
                            $condColors = ['good' => 'success', 'needs_attention' => 'warning', 'critical' => 'error'];
                            $condColor = $condColors[$rec->overall_condition] ?? 'ghost';
                            $condIcons = ['good' => 'o-check-circle', 'needs_attention' => 'o-exclamation-triangle', 'critical' => 'o-x-circle'];
                            $condIcon = $condIcons[$rec->overall_condition] ?? 'o-circle-stack';
                            $checklist = $rec->checklist_items ?? [];
                            $checklistLabels = [
                                'clean_dust' => 'Pembersihan Debu',
                                'check_thermal' => 'Pasta Thermal',
                                'antivirus_scan' => 'Scan Antivirus',
                                'disk_cleanup' => 'Disk Cleanup',
                                'os_update' => 'OS Update',
                                'network_test' => 'Tes Jaringan',
                                'backup_data' => 'Backup Data',
                            ];
                        @endphp
                        <div class="border border-base-content/10 rounded-xl p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-2 gap-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <x-mary-icon name="{{ $condIcon }}" class="w-5 h-5 text-{{ $condColor }}" />
                                    <span class="font-bold text-xs sm:text-sm">{{ $rec->maintenance_date->format('d F Y') }}</span>
                                    <span class="badge badge-{{ $condColor }} badge-sm text-xs">
                                        {{ ['good'=>'Baik','needs_attention'=>'Perlu Perhatian','critical'=>'Kritis'][$rec->overall_condition] ?? $rec->overall_condition }}
                                    </span>
                                </div>
                                <div class="text-xs opacity-50 font-mono">{{ $rec->period }}</div>
                            </div>
                            <div class="text-xs text-base-content/60 mb-2">
                                Teknisi: <span class="font-semibold">{{ $rec->technician->name ?? 'Unknown' }}</span>
                                @if($rec->is_user_signed)
                                    &nbsp;|&nbsp; TTD: <span class="font-semibold text-success">{{ $rec->user_sign_name ?: 'Sudah' }}</span>
                                @endif
                            </div>
                            @if(!empty($checklist))
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($checklistLabels as $key => $label)
                                        @if(isset($checklist[$key]))
                                            <span class="badge badge-sm {{ $checklist[$key] ? 'badge-success' : 'badge-ghost opacity-40' }} gap-1 text-[11px]">
                                                <x-mary-icon name="{{ $checklist[$key] ? 'o-check' : 'o-x-mark' }}" class="w-3 h-3" />
                                                {{ $label }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if($rec->notes)
                                <div class="text-xs bg-base-200 rounded-lg p-2 opacity-80 whitespace-pre-wrap">
                                    {{ $rec->notes }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end mt-4 pt-3 border-t border-base-content/10">
                <button type="button" wire:click="$set('showHistoryModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Tutup</button>
            </div>
        @endif
    </x-mary-modal>

    <!-- ======================================================= -->
    <!-- MODAL: TAMBAH / EDIT PERANGKAT PC -->
    <!-- ======================================================= -->
    <x-mary-modal wire:model="showDeviceModal" class="backdrop-blur-sm" box-class="max-w-lg p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4 flex items-center gap-2">
            <x-mary-icon name="o-computer-desktop" class="text-warning w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" />
            <span>{{ $editingDeviceId ? 'Edit Data PC' : 'Tambah PC Baru' }}</span>
        </h3>
        <form wire:submit="saveDevice" class="space-y-3 sm:space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Nama User / Pemakai *</label>
                    <x-mary-input wire:model="devUserName" placeholder="Contoh: Inge" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Comp Name / Hostname *</label>
                    <x-mary-input wire:model="devCompName" placeholder="Contoh: ACCT-01" />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Departemen</label>
                    <select wire:model="devDepartmentId" class="select select-bordered w-full select-sm">
                        <option value="">-- Pilih --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Lokasi Unit *</label>
                    <select wire:model="devLocation" class="select select-bordered w-full select-sm">
                        <option value="kantor">Kantor</option>
                        <option value="pabrik">Pabrik</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Tipe Perangkat</label>
                    <select wire:model="devDeviceType" class="select select-bordered w-full select-sm">
                        <option value="PC Desktop">PC Desktop</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Server">Server</option>
                        <option value="All-in-One">All-in-One</option>
                    </select>
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Status</label>
                    <select wire:model="devStatus" class="select select-bordered w-full select-sm">
                        <option value="active">Aktif</option>
                        <option value="maintenance">Dalam Maintenance</option>
                        <option value="broken">Rusak</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Sistem Operasi</label>
                <x-mary-input wire:model="devOperatingSystem" placeholder="Contoh: Windows 10 Pro 64-bit" />
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Spesifikasi (RAM / Storage / Processor)</label>
                <textarea wire:model="devSpecs" rows="2" class="textarea textarea-bordered w-full text-sm"
                    placeholder="Intel Core i5-10400, RAM 8GB, SSD 256GB"></textarea>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Catatan Tambahan</label>
                <textarea wire:model="devNotes" rows="2" class="textarea textarea-bordered w-full text-sm"
                    placeholder="PC milik bagian IT, ada UPS, dll..."></textarea>
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showDeviceModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md w-full sm:w-auto">
                    <x-mary-icon name="o-check" class="w-4 h-4" />
                    Simpan
                </button>
            </div>
        </form>
    </x-mary-modal>

    <!-- ======================================================= -->
    <!-- MODAL: IMPORT DATA -->
    <!-- ======================================================= -->
    <x-mary-modal wire:model="showImportModal" class="backdrop-blur-sm" box-class="max-w-lg p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <h3 class="font-bold text-base sm:text-lg mb-2 flex items-center gap-2">
            <x-mary-icon name="o-arrow-up-tray" class="text-info w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" />
            <span>Import Data PC (Copy Paste dari Excel)</span>
        </h3>
        <p class="text-xs sm:text-sm opacity-60 mb-3 sm:mb-4">
            Copy kolom dari Excel (format: <code class="bg-base-200 px-1 rounded">User | Comp Name | Dept</code>), lalu paste ke kotak di bawah.
        </p>
        <div class="space-y-3">
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Lokasi Unit</label>
                <select wire:model="importLocation" class="select select-bordered w-full select-sm">
                    <option value="kantor">Kantor</option>
                    <option value="pabrik">Pabrik</option>
                </select>
            </div>
            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Data (satu baris per PC, pisah tab/koma)</label>
                <textarea wire:model="importRawText" rows="8" class="textarea textarea-bordered w-full text-xs sm:text-sm font-mono"
                    placeholder="Stephen&#9;DESKTOP-RU1L62Q&#9;IT&#10;Inge&#9;ACCT-01&#9;FA&#10;Lusi&#9;FIN-01&#9;FA"></textarea>
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-2">
                <button type="button" wire:click="$set('showImportModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button wire:click="processImport" class="btn btn-info btn-sm px-6 w-full sm:w-auto">
                    <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4" />
                    Proses Import
                </button>
            </div>
        </div>
    </x-mary-modal>

</div>
