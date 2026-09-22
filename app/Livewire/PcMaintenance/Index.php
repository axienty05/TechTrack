<?php

namespace App\Livewire\PcMaintenance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ComputerDevice;
use App\Models\PcMaintenanceRecord;
use App\Models\Department;
use App\Models\RoutineSchedule;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination, WithFileUploads, Toast;

    // Filter & Tab State
    public string $activeTab = 'kantor'; // 'kantor' or 'pabrik'
    public string $search = '';
    public string $departmentFilter = '';
    public string $statusFilter = 'all'; // 'all', 'completed', 'pending'
    public string $selectedPeriod = '';

    // Maintenance Form Modal State
    public bool $showMaintenanceModal = false;
    public ?int $selectedDeviceId = null;
    public string $maintCompName = '';
    public string $maintUserName = '';
    public string $maintDate = '';
    public string $maintPeriod = '';
    public string $maintNotes = '';
    public string $maintUserSignName = '';
    public bool $maintIsUserSigned = true;
    public string $maintCondition = 'good';
    public array $checklist = [
        'clean_dust' => true,
        'check_thermal' => false,
        'antivirus_scan' => true,
        'disk_cleanup' => true,
        'os_update' => false,
        'network_test' => true,
        'backup_data' => false,
    ];
    public bool $maintHasExisting = false;

    // History Modal State
    public bool $showHistoryModal = false;
    public ?ComputerDevice $historyDevice = null;

    // Device Master CRUD Modal State
    public bool $showDeviceModal = false;
    public ?int $editingDeviceId = null;
    public string $devCompName = '';
    public string $devUserName = '';
    public ?int $devDepartmentId = null;
    public string $devLocation = 'kantor';
    public string $devDeviceType = 'PC Desktop';
    public string $devOperatingSystem = '';
    public string $devSpecs = '';
    public string $devStatus = 'active';
    public string $devNotes = '';

    // Bulk Import Modal State
    public bool $showImportModal = false;
    public string $importRawText = '';
    public string $importLocation = 'kantor';

    public function mount()
    {
        $this->selectedPeriod = date('Y-m');
        $this->maintDate = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab = in_array($tab, ['kantor', 'pabrik']) ? $tab : 'kantor';
        $this->resetPage();
    }

    public function updatingDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    // --- Maintenance Actions ---
    public function openMaintenanceModal(int $deviceId)
    {
        $device = ComputerDevice::with(['latestMaintenance', 'department'])->findOrFail($deviceId);
        $this->selectedDeviceId = $device->id;
        $this->maintCompName = $device->comp_name;
        $this->maintUserName = $device->user_name;
        $this->maintDate = date('Y-m-d');
        $this->maintPeriod = $this->selectedPeriod ?: date('Y-m');
        $this->maintUserSignName = $device->user_name;
        $this->maintIsUserSigned = true;
        $this->maintCondition = 'good';
        $this->maintNotes = '';

        // Jika sudah ada maintenance di periode ini, load data sebelumnya
        $existing = PcMaintenanceRecord::where('computer_device_id', $device->id)
            ->where('period', $this->maintPeriod)
            ->latest('maintenance_date')
            ->first();

        $this->maintHasExisting = (bool) $existing;

        if ($existing) {
            $this->maintDate = $existing->maintenance_date->format('Y-m-d');
            $this->maintNotes = $existing->notes ?? '';
            $this->maintUserSignName = $existing->user_sign_name ?? $device->user_name;
            $this->maintIsUserSigned = (bool) $existing->is_user_signed;
            $this->maintCondition = $existing->overall_condition ?? 'good';
            if (is_array($existing->checklist_items)) {
                $this->checklist = array_merge($this->checklist, $existing->checklist_items);
            }
        } else {
            // Default checklist tercentang standar
            $this->checklist = [
                'clean_dust' => true,
                'check_thermal' => false,
                'antivirus_scan' => true,
                'disk_cleanup' => true,
                'os_update' => false,
                'network_test' => true,
                'backup_data' => false,
            ];
        }

        $this->showMaintenanceModal = true;
    }

    public function deleteCurrentMaintenance()
    {
        if ($this->selectedDeviceId) {
            $period = $this->maintPeriod ?: ($this->selectedPeriod ?: date('Y-m'));
            PcMaintenanceRecord::where('computer_device_id', $this->selectedDeviceId)
                ->where('period', $period)
                ->delete();

            $this->showMaintenanceModal = false;
            $this->success("Status maintenance untuk periode {$period} berhasil direset / dihapus!");
        }
    }

    public function deleteRecord(int $recordId)
    {
        $rec = PcMaintenanceRecord::findOrFail($recordId);
        $devId = $rec->computer_device_id;
        $rec->delete();

        $this->historyDevice = ComputerDevice::with(['department', 'maintenanceRecords.technician'])->find($devId);
        $this->success('Catatan riwayat maintenance berhasil dihapus.');
    }

    public function saveMaintenance()
    {
        $this->validate([
            'maintDate' => 'required|date',
            'maintPeriod' => 'required|string',
        ]);

        $record = PcMaintenanceRecord::updateOrCreate(
            [
                'computer_device_id' => $this->selectedDeviceId,
                'period' => $this->maintPeriod,
            ],
            [
                'technician_id' => auth()->id(),
                'maintenance_date' => $this->maintDate,
                'checklist_items' => $this->checklist,
                'notes' => $this->maintNotes,
                'user_sign_name' => $this->maintUserSignName ?: $this->maintUserName,
                'is_user_signed' => $this->maintIsUserSigned,
                'overall_condition' => $this->maintCondition,
            ]
        );

        $this->success("Maintenance untuk {$this->maintCompName} ({$this->maintUserName}) berhasil disimpan!");
        $this->showMaintenanceModal = false;
    }

    // --- History Action ---
    public function openHistoryModal(int $deviceId)
    {
        $this->historyDevice = ComputerDevice::with(['department', 'maintenanceRecords.technician'])->findOrFail($deviceId);
        $this->showHistoryModal = true;
    }

    // --- Device Master CRUD ---
    public function openCreateDeviceModal()
    {
        $this->reset([
            'editingDeviceId',
            'devCompName',
            'devUserName',
            'devSpecs',
            'devNotes',
            'devOperatingSystem',
        ]);
        $this->devLocation = $this->activeTab;
        $this->devDeviceType = 'PC Desktop';
        $this->devStatus = 'active';
        $firstDept = Department::first();
        $this->devDepartmentId = $firstDept ? $firstDept->id : null;
        $this->showDeviceModal = true;
    }

    public function openEditDeviceModal(int $id)
    {
        $dev = ComputerDevice::findOrFail($id);
        $this->editingDeviceId = $dev->id;
        $this->devCompName = $dev->comp_name;
        $this->devUserName = $dev->user_name;
        $this->devDepartmentId = $dev->department_id;
        $this->devLocation = $dev->location;
        $this->devDeviceType = $dev->device_type;
        $this->devOperatingSystem = $dev->operating_system ?? '';
        $this->devSpecs = $dev->specs ?? '';
        $this->devStatus = $dev->status ?? 'active';
        $this->devNotes = $dev->notes ?? '';
        $this->showDeviceModal = true;
    }

    public function saveDevice()
    {
        $this->validate([
            'devCompName' => 'required|string|max:100',
            'devUserName' => 'required|string|max:150',
            'devLocation' => 'required|in:kantor,pabrik',
            'devDepartmentId' => 'nullable|exists:departments,id',
        ]);

        $data = [
            'comp_name' => $this->devCompName,
            'user_name' => $this->devUserName,
            'department_id' => $this->devDepartmentId,
            'location' => $this->devLocation,
            'device_type' => $this->devDeviceType,
            'operating_system' => $this->devOperatingSystem,
            'specs' => $this->devSpecs,
            'status' => $this->devStatus,
            'notes' => $this->devNotes,
        ];

        if ($this->editingDeviceId) {
            ComputerDevice::findOrFail($this->editingDeviceId)->update($data);
            $this->success("Data PC '{$this->devCompName}' berhasil diperbarui!");
        } else {
            ComputerDevice::create($data);
            $this->success("Perangkat PC baru '{$this->devCompName}' berhasil ditambahkan!");
        }

        $this->showDeviceModal = false;
    }

    public function deleteDevice(int $id)
    {
        $dev = ComputerDevice::findOrFail($id);
        $name = $dev->comp_name;
        $dev->delete();
        $this->success("Perangkat {$name} berhasil dihapus!");
    }

    // --- Bulk Import Action ---
    public function openImportModal()
    {
        $this->importRawText = '';
        $this->importLocation = $this->activeTab;
        $this->showImportModal = true;
    }

    public function processImport()
    {
        $lines = explode("\n", trim($this->importRawText));
        if (empty($lines) || empty(trim($lines[0]))) {
            $this->error('Teks input tidak boleh kosong.');
            return;
        }

        $count = 0;
        foreach ($lines as $line) {
            $parts = preg_split('/[\t,;]+/', trim($line));
            if (count($parts) >= 2) {
                $user = trim($parts[0]);
                $comp = trim($parts[1]);
                $deptCode = isset($parts[2]) ? strtoupper(trim($parts[2])) : null;

                $dept = null;
                if ($deptCode) {
                    $dept = Department::where('code', $deptCode)->orWhere('name', 'like', "%{$deptCode}%")->first();
                }

                ComputerDevice::updateOrCreate(
                    ['comp_name' => $comp, 'location' => $this->importLocation],
                    [
                        'user_name' => $user,
                        'department_id' => $dept ? $dept->id : null,
                        'device_type' => 'PC Desktop',
                        'status' => 'active',
                    ]
                );
                $count++;
            }
        }

        $this->success("Berhasil mengimpor {$count} unit komputer ke unit {$this->importLocation}!");
        $this->showImportModal = false;
    }

    // --- Export to CSV matching Excel columns ---
    public function exportExcel()
    {
        $locationLabel = ucfirst($this->activeTab);
        $period = $this->selectedPeriod ?: date('Y-m');
        $devices = ComputerDevice::with(['department', 'maintenanceRecords' => function ($q) use ($period) {
            $q->where('period', $period);
        }])
        ->where('location', $this->activeTab)
        ->orderBy('id')
        ->get();

        $filename = "Jadwal_Maintenance_PC_PT_Aneka_Coffee_Industry_{$this->activeTab}_{$period}.csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($devices, $locationLabel, $period) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8 compatibility with MS Excel
            fputs($file, "\xEF\xBB\xBF");

            // Judul Header Dokumen
            fputcsv($file, ["Jadwal Maintenance PC PT. Aneka Coffee Industry ({$locationLabel}) - Periode {$period}"]);
            fputcsv($file, []);

            // Baris Header Kolom persis seperti Excel
            fputcsv($file, ['#', 'User', 'Comp Name', 'Dept', 'Tanggal', 'Notes', 'Ttd user']);

            $no = 1;
            foreach ($devices as $d) {
                $record = $d->maintenanceRecords->first();
                $tgl = $record ? $record->maintenance_date->format('d/m/Y') : '';
                $notes = $record ? $record->notes : '';
                $ttd = ($record && $record->is_user_signed) ? ($record->user_sign_name ?: 'Sudah Ttd') : '';

                fputcsv($file, [
                    $no++,
                    $d->user_name,
                    $d->comp_name,
                    $d->department ? $d->department->name : '-',
                    $tgl,
                    $notes,
                    $ttd,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function render()
    {
        $period = $this->selectedPeriod ?: date('Y-m');
        $term   = '%' . trim($this->search) . '%';

        // Cari Routine Schedule aktif yang berlaku untuk tab lokasi ini
        $activeSchedule = RoutineSchedule::where('is_active', true)
            ->where(function ($q) {
                $q->where('pc_location', $this->activeTab)
                  ->orWhere('pc_location', 'semua');
            })
            ->latest('id') // ambil yang paling baru jika ada lebih dari 1
            ->first();

        // Query Perangkat
        $query = ComputerDevice::with([
            'department',
            'maintenanceRecords' => function ($q) use ($period) {
                $q->where('period', $period)->latest('maintenance_date');
            },
            'latestMaintenance',
        ])
        ->where('location', $this->activeTab)
        ->when($this->search, function ($q) use ($term) {
            $q->where(function ($sub) use ($term) {
                $sub->where('comp_name', 'like', $term)
                    ->orWhere('user_name', 'like', $term);
            });
        })
        ->when($this->departmentFilter, function ($q) {
            $q->where('department_id', $this->departmentFilter);
        });

        // Filter status selesai/belum di periode ini
        if ($this->statusFilter === 'completed') {
            $query->whereHas('maintenanceRecords', function ($q) use ($period) {
                $q->where('period', $period);
            });
        } elseif ($this->statusFilter === 'pending') {
            $query->whereDoesntHave('maintenanceRecords', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        $devices = $query->orderBy('id')->paginate(15);

        // Hitung due date info per device jika ada jadwal aktif
        $dueDateMap = [];
        if ($activeSchedule) {
            foreach ($devices as $device) {
                $lastDate  = $device->latestMaintenance
                    ? Carbon::parse($device->latestMaintenance->maintenance_date)
                    : null;

                $nextDue   = $activeSchedule->nextDueDate($lastDate);
                $daysLeft  = (int) Carbon::today()->diffInDays($nextDue, false); // negatif = overdue
                $isOverdue = $nextDue->isPast() && !$nextDue->isToday();
                $isToday   = $nextDue->isToday();
                $neverMaintained = $lastDate === null;

                $dueDateMap[$device->id] = [
                    'next_due'        => $nextDue,
                    'days_left'       => $daysLeft,
                    'is_overdue'      => $isOverdue,
                    'is_today'        => $isToday,
                    'never_maintained'=> $neverMaintained,
                ];
            }
        }

        // Statistik Cepat
        $totalKantor  = ComputerDevice::kantor()->count();
        $totalPabrik  = ComputerDevice::pabrik()->count();
        $completedCurrentPeriod = PcMaintenanceRecord::where('period', $period)
            ->whereHas('computerDevice', function ($q) {
                $q->where('location', $this->activeTab);
            })->count();

        $totalActiveTab  = $this->activeTab === 'kantor' ? $totalKantor : $totalPabrik;
        $progressPercent = $totalActiveTab > 0 ? round(($completedCurrentPeriod / $totalActiveTab) * 100) : 0;

        return view('livewire.pc-maintenance.index', [
            'devices'               => $devices,
            'departments'           => Department::orderBy('name')->get(),
            'totalKantor'           => $totalKantor,
            'totalPabrik'           => $totalPabrik,
            'completedCurrentPeriod'=> $completedCurrentPeriod,
            'totalActiveTab'        => $totalActiveTab,
            'progressPercent'       => $progressPercent,
            'activeSchedule'        => $activeSchedule,
            'dueDateMap'            => $dueDateMap,
        ]);
    }
}
