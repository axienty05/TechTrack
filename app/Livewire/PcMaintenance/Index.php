<?php

namespace App\Livewire\PcMaintenance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Barang;
use App\Models\Pemakai;
use App\Models\PcMaintenanceRecord;
use App\Models\Department;
use App\Models\Category;
use App\Models\WorkLog;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination, Toast;

    // Filter & Tab State
    public string $activeTab      = 'kantor'; // 'kantor' or 'pabrik'
    public string $search         = '';
    public string $departmentFilter = '';
    public string $statusFilter   = 'all'; // 'all', 'completed', 'pending'
    public string $selectedPeriod = '';
    public bool   $showExcluded   = false;  // Toggle tampilkan user yang di-exclude (laptop)

    // Maintenance Form Modal State
    public bool $showMaintenanceModal = false;
    public ?int $selectedBarangId = null;
    public ?int $selectedPemakaiId = null;
    public string $maintCompName = '';
    public string $maintNamaBarang = '';
    public string $maintUserName = '';
    public string $maintDate = '';
    public string $maintPeriod = '';
    public string $maintNotes = '';
    public string $maintUserSignName = '';
    public bool $maintIsUserSigned = true;
    public string $maintCondition = 'good';
    public array $checklist = [
        'clean_dust'      => true,
        'check_thermal'   => false,
        'antivirus_scan'  => true,
        'disk_cleanup'    => true,
        'os_update'       => false,
        'network_test'    => true,
        'backup_data'     => false,
    ];
    public bool $maintHasExisting = false;

    // History Modal State
    public bool $showHistoryModal = false;
    public ?Barang $historyBarang = null;

    protected array $kantorDeptCodes = ['IT', 'FA', 'HRD', 'RND', 'LAB', 'ISO'];
    protected array $pabrikDeptCodes = ['EXIM', 'SC', 'PROD', 'TEK'];

    public function mount()
    {
        $this->selectedPeriod = date('Y-m');
        $this->maintDate      = date('Y-m-d');
    }

    public function updatingSearch()           { $this->resetPage(); }
    public function updatingActiveTab()        { $this->resetPage(); }
    public function updatingDepartmentFilter() { $this->resetPage(); }
    public function updatingStatusFilter()     { $this->resetPage(); }
    public function updatingSelectedPeriod()   { $this->resetPage(); }
    public function updatingShowExcluded()     { $this->resetPage(); }

    public function resetFilters()
    {
        $this->search           = '';
        $this->departmentFilter = '';
        $this->statusFilter     = 'all';
        $this->showExcluded     = false;
        $this->resetPage();
    }

    public function getFormattedPeriodProperty(): string
    {
        try {
            $carbon = Carbon::createFromFormat('Y-m', $this->selectedPeriod ?: date('Y-m'));
            $bulanIndo = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            return $bulanIndo[(int)$carbon->format('n')] . ' ' . $carbon->format('Y');
        } catch (\Throwable $e) {
            return $this->selectedPeriod ?: date('Y-m');
        }
    }

    public function prevPeriod()
    {
        $current = $this->selectedPeriod ?: date('Y-m');
        $this->selectedPeriod = Carbon::createFromFormat('Y-m', $current)->subMonth()->format('Y-m');
        $this->resetPage();
    }

    public function nextPeriod()
    {
        $current = $this->selectedPeriod ?: date('Y-m');
        $this->selectedPeriod = Carbon::createFromFormat('Y-m', $current)->addMonth()->format('Y-m');
        $this->resetPage();
    }

    public function setPeriodToday()
    {
        $this->selectedPeriod = date('Y-m');
        $this->resetPage();
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab        = in_array($tab, ['kantor', 'pabrik']) ? $tab : 'kantor';
        $this->departmentFilter = '';
        $this->resetPage();
    }

    // --- Exclude / Restore dari PC Maintenance ---
    public function excludeFromMaintenance(int $pemakaiId)
    {
        $pemakai = Pemakai::findOrFail($pemakaiId);
        $pemakai->update(['exclude_pc_maintenance' => true]);
        $this->success("{$pemakai->nama} dihapus dari daftar PC Maintenance.");
    }

    public function restoreToMaintenance(int $pemakaiId)
    {
        $pemakai = Pemakai::findOrFail($pemakaiId);
        $pemakai->update(['exclude_pc_maintenance' => false]);
        $this->success("{$pemakai->nama} dikembalikan ke daftar PC Maintenance.");
    }

    // --- Maintenance Actions ---
    public function openMaintenanceModal(int $barangId)
    {
        $barang = Barang::with(['pemakai.department'])->findOrFail($barangId);
        $this->selectedBarangId  = $barang->id;
        $this->selectedPemakaiId = $barang->m_pemakai_id;
        $this->maintCompName     = $barang->pemakai?->comp_name ?: $barang->nama_barang;
        $this->maintNamaBarang   = $barang->nama_barang;
        $this->maintUserName     = $barang->pemakai?->nama ?: 'User';
        $this->maintDate         = date('Y-m-d');
        $this->maintPeriod       = $this->selectedPeriod ?: date('Y-m');
        $this->maintUserSignName = $this->maintUserName;
        $this->maintIsUserSigned = true;
        $this->maintCondition    = 'good';
        $this->maintNotes        = '';

        // Cek jika sudah ada maintenance di periode ini
        $existing = PcMaintenanceRecord::where('m_barang_id', $barang->id)
            ->where('period', $this->maintPeriod)
            ->latest('maintenance_date')
            ->first();

        $this->maintHasExisting = (bool) $existing;

        if ($existing) {
            $this->maintDate         = $existing->maintenance_date ? $existing->maintenance_date->format('Y-m-d') : date('Y-m-d');
            $this->maintNotes        = $existing->notes ?? '';
            $this->maintUserSignName = $existing->user_sign_name ?? $this->maintUserName;
            $this->maintIsUserSigned = (bool) $existing->is_user_signed;
            $this->maintCondition    = $existing->overall_condition ?? 'good';
            if (is_array($existing->checklist_items)) {
                $this->checklist = array_merge([
                    'clean_dust'     => false,
                    'check_thermal'  => false,
                    'antivirus_scan' => false,
                    'disk_cleanup'   => false,
                    'os_update'      => false,
                    'network_test'   => false,
                    'backup_data'    => false,
                ], $existing->checklist_items);
            }
        } else {
            $this->checklist = [
                'clean_dust'     => true,
                'check_thermal'  => false,
                'antivirus_scan' => true,
                'disk_cleanup'   => true,
                'os_update'      => false,
                'network_test'   => true,
                'backup_data'    => false,
            ];
        }

        $this->showMaintenanceModal = true;
    }

    public function deleteCurrentMaintenance()
    {
        if ($this->selectedBarangId) {
            $period = $this->maintPeriod ?: ($this->selectedPeriod ?: date('Y-m'));
            $record = PcMaintenanceRecord::where('m_barang_id', $this->selectedBarangId)
                ->where('period', $period)
                ->first();

            if ($record) {
                if ($record->work_log_id) {
                    WorkLog::where('id', $record->work_log_id)->delete();
                }
                $record->delete();
            }

            $this->showMaintenanceModal = false;
            $this->success("Status maintenance untuk periode {$period} berhasil direset!");
        }
    }

    public function deleteRecord(int $recordId)
    {
        $rec      = PcMaintenanceRecord::findOrFail($recordId);
        $barangId = $rec->m_barang_id;
        if ($rec->work_log_id) {
            WorkLog::where('id', $rec->work_log_id)->delete();
        }
        $rec->delete();

        if ($barangId) {
            $this->historyBarang = Barang::with(['pemakai.department', 'pcMaintenances.technician'])->find($barangId);
        }
        $this->success('Catatan riwayat maintenance berhasil dihapus.');
    }

    public function saveMaintenance()
    {
        $this->validate([
            'maintDate'   => 'required|date',
            'maintPeriod' => 'required|string',
        ]);

        $barang      = Barang::with('pemakai.department')->findOrFail($this->selectedBarangId);
        $compName    = $barang->pemakai?->comp_name ?: $barang->nama_barang;
        $pemakaiName = $this->maintUserSignName ?: ($barang->pemakai?->nama ?: 'User');

        // Ringkasan tindakan dari checklist untuk worklog
        $checklistLabels = [
            'clean_dust'     => 'Pembersihan debu & fisik',
            'check_thermal'  => 'Ganti pasta thermal',
            'antivirus_scan' => 'Scan antivirus',
            'disk_cleanup'   => 'Disk cleanup',
            'os_update'      => 'Update OS',
            'network_test'   => 'Tes koneksi jaringan',
            'backup_data'    => 'Backup data',
        ];

        $actionsDone = [];
        foreach ($this->checklist as $key => $val) {
            if ($val && isset($checklistLabels[$key])) {
                $actionsDone[] = $checklistLabels[$key];
            }
        }
        $summaryAction = implode(', ', $actionsDone);
        if (!empty($this->maintNotes)) {
            $summaryAction .= ($summaryAction ? ". " : "") . "Catatan: " . $this->maintNotes;
        }

        // Kategori MTC atau PC untuk WorkLog
        $category = Category::where('code', 'MTC')->orWhere('code', 'PC')->first()
            ?? Category::first();

        // 1. Simpan / update ke WorkLog harian (Otomatis masuk Work Log)
        $workLog = WorkLog::updateOrCreate(
            [
                'device_identifier' => $compName,
                'task_type'         => 'preventive',
                'started_at'        => $this->maintDate . ' 08:30:00',
            ],
            [
                'user_id'           => auth()->id() ?: 1,
                'category_id'       => $category?->id,
                'department_id'     => $barang->pemakai?->department_id,
                'title'             => "Maintenance Berkala PC - {$compName} ({$pemakaiName})",
                'description'       => "Perawatan rutin berkala PC Desktop untuk periode {$this->maintPeriod}. Perangkat: {$barang->nama_barang}.",
                'action_taken'      => $summaryAction ?: 'Pemeriksaan dan perawatan rutin PC selesai.',
                'requester_name'    => $pemakaiName,
                'status'            => 'completed',
                'completed_at'      => $this->maintDate . ' 09:30:00',
                'priority'          => match ($this->maintCondition) {
                    'critical'         => 'critical',
                    'needs_attention'  => 'high',
                    default            => 'medium',
                },
            ]
        );

        // 2. Simpan / update ke PcMaintenanceRecord
        PcMaintenanceRecord::updateOrCreate(
            [
                'm_barang_id' => $barang->id,
                'period'      => $this->maintPeriod,
            ],
            [
                'work_log_id'       => $workLog->id,
                'technician_id'     => auth()->id() ?: 1,
                'maintenance_date'  => $this->maintDate,
                'checklist_items'   => $this->checklist,
                'notes'             => $this->maintNotes,
                'user_sign_name'    => $pemakaiName,
                'is_user_signed'    => $this->maintIsUserSigned,
                'overall_condition' => $this->maintCondition,
            ]
        );

        $this->success("Maintenance untuk {$compName} ({$pemakaiName}) berhasil disimpan & otomatis tercatat di Work Log!");
        $this->showMaintenanceModal = false;
    }

    // --- History Action ---
    public function openHistoryModal(int $barangId)
    {
        $this->historyBarang = Barang::with(['pemakai.department', 'pcMaintenances.technician'])->findOrFail($barangId);
        $this->showHistoryModal = true;
    }

    // --- Export CSV / Excel ---
    public function exportExcel()
    {
        $period          = $this->selectedPeriod ?: date('Y-m');
        $currentDeptCodes = $this->activeTab === 'kantor' ? $this->kantorDeptCodes : $this->pabrikDeptCodes;

        $pemakais = Pemakai::with([
            'department',
            'barangs' => fn($q) => $q->where('kategori', 'komputer')->where('status', 'aktif')
                ->with(['pcMaintenances' => fn($q2) => $q2->where('period', $period)->latest('maintenance_date')]),
        ])
        ->where('status', true)
        ->whereHas('department', fn($q) => $q->whereIn('code', $currentDeptCodes))
        ->orderBy('nama')
        ->get();

        $filename = "PC_Maintenance_{$this->activeTab}_{$period}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($pemakais) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8 Excel

            fputcsv($file, [
                'No', 'User', 'Comp Name', 'Dept',
                'Tanggal Maintenance', 'Kondisi', 'Catatan', 'Ttd User',
            ], ",", '"', "\\");

            $no = 1;
            foreach ($pemakais as $p) {
                $barang = $p->barangs->first();
                $record = $barang?->pcMaintenances->first();
                $tgl    = $record && $record->maintenance_date ? $record->maintenance_date->format('d/m/Y') : 'Belum';
                $notes  = $record ? $record->notes : '';
                $ttd    = ($record && $record->is_user_signed) ? ($record->user_sign_name ?: 'Sudah TTD') : 'Belum';
                $cond   = $record ? ucfirst($record->overall_condition) : '-';

                fputcsv($file, [
                    $no++,
                    $p->nama,
                    $barang ? ($p->comp_name ?: $barang->nama_barang) : '-',
                    $p->department?->code ?: '-',
                    $tgl, $cond, $notes, $ttd,
                ], ",", '"', "\\");
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function render()
    {
        $period           = $this->selectedPeriod ?: date('Y-m');
        $term             = '%' . trim($this->search) . '%';
        $currentDeptCodes = $this->activeTab === 'kantor' ? $this->kantorDeptCodes : $this->pabrikDeptCodes;

        // Query utama: berbasis Pemakai — tampilkan semua user, komputer menyusul
        $query = Pemakai::with([
            'department',
            'barangs' => fn($q) => $q->where('kategori', 'komputer')->where('status', 'aktif')
                ->with(['pcMaintenances' => fn($q2) => $q2->where('period', $period)->latest('maintenance_date')]),
        ])
        ->where('status', true)
        ->when(!$this->showExcluded, fn($q) => $q->where('exclude_pc_maintenance', false))
        ->whereHas('department', fn($q) => $q->whereIn('code', $currentDeptCodes))
        ->when($this->search, fn($q) => $q->where(fn($sub) => $sub
            ->where('nama', 'like', $term)
            ->orWhere('comp_name', 'like', $term)
        ))
        ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter));

        // Status filter: 'completed' = punya barang + punya record periode ini, 'pending' = belum
        if ($this->statusFilter === 'completed') {
            $query->whereHas('barangs', fn($q) => $q->where('kategori', 'komputer')->where('status', 'aktif')
                ->whereHas('pcMaintenances', fn($q2) => $q2->where('period', $period))
            );
        } elseif ($this->statusFilter === 'pending') {
            $query->where(fn($q) =>
                // Pemakai tanpa komputer ATAU pemakai dengan komputer tapi belum di-maintenance
                $q->whereDoesntHave('barangs', fn($bq) => $bq->where('kategori', 'komputer')->where('status', 'aktif'))
                  ->orWhereHas('barangs', fn($bq) => $bq->where('kategori', 'komputer')->where('status', 'aktif')
                      ->whereDoesntHave('pcMaintenances', fn($mq) => $mq->where('period', $period))
                  )
            );
        }

        $pemakais = $query->orderBy('nama')->paginate(15);

        // Department options for current active tab
        $departments = Department::whereIn('code', $currentDeptCodes)->orderBy('name')->get();

        // Statistik: hitung total pemakai aktif yang tidak di-exclude
        $totalKantor = Pemakai::where('status', true)
            ->where('exclude_pc_maintenance', false)
            ->whereHas('department', fn($q) => $q->whereIn('code', $this->kantorDeptCodes))
            ->count();

        $totalPabrik = Pemakai::where('status', true)
            ->where('exclude_pc_maintenance', false)
            ->whereHas('department', fn($q) => $q->whereIn('code', $this->pabrikDeptCodes))
            ->count();

        // Total user yang di-exclude pada tab yang aktif saat ini
        $totalExcluded = Pemakai::where('status', true)
            ->where('exclude_pc_maintenance', true)
            ->whereHas('department', fn($q) => $q->whereIn('code', $currentDeptCodes))
            ->count();

        // Completed: pemakai yang punya komputer + sudah di-maintenance periode ini
        $completedCurrentPeriod = Pemakai::where('status', true)
            ->whereHas('department', fn($q) => $q->whereIn('code', $currentDeptCodes))
            ->whereHas('barangs', fn($q) => $q->where('kategori', 'komputer')->where('status', 'aktif')
                ->whereHas('pcMaintenances', fn($q2) => $q2->where('period', $period))
            )->count();

        $totalActiveTab   = $this->activeTab === 'kantor' ? $totalKantor : $totalPabrik;
        $progressPercent  = $totalActiveTab > 0 ? round(($completedCurrentPeriod / $totalActiveTab) * 100) : 0;

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        try {
            $carbonPeriod    = Carbon::createFromFormat('Y-m', $period);
            $formattedPeriod = $bulanIndo[(int)$carbonPeriod->format('n')] . ' ' . $carbonPeriod->format('Y');
        } catch (\Throwable $e) {
            $formattedPeriod = $period;
        }

        return view('livewire.pc-maintenance.index', [
            'pemakais'               => $pemakais,
            'departments'            => $departments,
            'totalKantor'            => $totalKantor,
            'totalPabrik'            => $totalPabrik,
            'totalExcluded'          => $totalExcluded,
            'completedCurrentPeriod' => $completedCurrentPeriod,
            'totalActiveTab'         => $totalActiveTab,
            'progressPercent'        => $progressPercent,
            'formattedPeriod'        => $formattedPeriod,
        ]);
    }
}
