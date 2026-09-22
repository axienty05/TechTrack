<?php

namespace App\Livewire\WorkLogs;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\WorkLog;
use App\Models\Category;
use App\Models\Department;
use App\Models\LogAttachment;
use App\Models\ComputerDevice;
use App\Models\PcMaintenanceRecord;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, WithFileUploads, Toast;

    // Filters
    public string $search = '';
    public string $statusFilter = '';
    public string $categoryFilter = '';
    public string $departmentFilter = '';
    public string $priorityFilter = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $datePreset = '';

    // Modals
    public bool $showModal = false;
    public bool $showDetailModal = false;
    public bool $showImageModal = false;

    // Preview Image Modal Data
    public array $previewImage = [
        'url' => '',
        'title' => '',
        'caption' => '',
        'type' => '',
    ];

    // Detail Modal Data
    public ?WorkLog $detailLog = null;

    // Form Fields
    public ?int $workLogId = null;
    public string $ticket_number = '';
    public string $title = '';
    public ?int $category_id = null;
    public ?int $department_id = null;
    public string $task_type = 'reactive';
    public string $requester_name = '';
    public string $device_identifier = '';
    public string $priority = 'medium';
    public string $status = 'in_progress';
    public string $started_at = '';
    public string $completed_at = '';
    public string $description = '';
    public string $action_taken = '';

    // Auto-detected PC Maintenance device info
    public ?int $detectedDeviceId = null;
    public ?string $detectedDevSummary = null;
    public ?string $detectedDevLocation = null;

    // Attachments
    public $beforePhoto = null;
    public string $beforeCaption = '';
    public $afterPhoto = null;
    public string $afterCaption = '';
    public $newAttachments = [];
    public string $newAttachmentType = 'error_screenshot';
    public string $newAttachmentCaption = '';
    public array $existingAttachments = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'task_type' => 'required|in:reactive,preventive,administrative',
            'requester_name' => 'nullable|string|max:100',
            'device_identifier' => 'nullable|string|max:100',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:pending,in_progress,waiting_sparepart,escalated,completed,cancelled',
            'started_at' => 'required',
            'completed_at' => 'nullable',
            'description' => 'nullable|string',
            'action_taken' => 'nullable|string',
            'beforePhoto' => 'nullable|image|max:10240',
            'afterPhoto' => 'nullable|image|max:10240',
            'newAttachments.*' => 'nullable|image|max:10240', // max 10MB per image
        ];
    }

    public static function generateTicketNumber(): string
    {
        $datePrefix = date('Ymd');
        do {
            $suffix = str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
            $ticketNumber = "IT-{$datePrefix}-{$suffix}";
        } while (WorkLog::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }

    public function openCreateModal()
    {
        $this->reset([
            'workLogId', 'title', 'category_id', 'department_id',
            'requester_name', 'device_identifier', 'description', 'action_taken',
            'beforePhoto', 'beforeCaption', 'afterPhoto', 'afterCaption',
            'newAttachments', 'newAttachmentCaption', 'existingAttachments',
            'detectedDeviceId', 'detectedDevSummary', 'detectedDevLocation',
        ]);

        $this->ticket_number = self::generateTicketNumber();
        $this->task_type = 'reactive';
        $this->priority = 'medium';
        $this->status = 'in_progress';
        $this->started_at = now()->format('Y-m-d');
        $this->completed_at = '';
        $this->newAttachmentType = 'after';

        // Default to first category if available
        $firstCat = Category::first();
        if ($firstCat) {
            $this->category_id = $firstCat->id;
        }

        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $log = WorkLog::with('attachments')->findOrFail($id);

        $this->workLogId = $log->id;
        $this->ticket_number = $log->ticket_number;
        $this->title = $log->title;
        $this->category_id = $log->category_id;
        $this->department_id = $log->department_id;
        $this->task_type = $log->task_type ?? 'reactive';
        $this->requester_name = $log->requester_name ?? '';
        $this->device_identifier = $log->device_identifier ?? '';
        $this->priority = $log->priority ?? 'medium';
        $this->status = $log->status ?? 'in_progress';
        $this->started_at = $log->started_at ? $log->started_at->format('Y-m-d') : '';
        $this->completed_at = $log->completed_at ? $log->completed_at->format('Y-m-d') : '';
        $this->description = $log->description ?? '';
        $this->action_taken = $log->action_taken ?? '';

        $this->existingAttachments = $log->attachments->map(fn($a) => [
            'id' => $a->id,
            'url' => asset('storage/' . $a->file_path),
            'type' => $a->attachment_type,
            'caption' => $a->caption,
        ])->toArray();

        $this->beforePhoto = null;
        $this->beforeCaption = '';
        $this->afterPhoto = null;
        $this->afterCaption = '';
        $this->newAttachments = [];
        $this->newAttachmentCaption = '';
        $this->newAttachmentType = 'error_screenshot';

        $this->detectedDeviceId = null;
        $this->detectedDevSummary = null;
        $this->detectedDevLocation = null;
        $this->autoDetectPcDevice();

        $this->showModal = true;
    }

    public function updatedTitle($value): void
    {
        $this->autoDetectPcDevice();
    }

    public function updatedDeviceIdentifier($value): void
    {
        $this->autoDetectPcDevice();
    }

    public function updatedRequesterName($value): void
    {
        $this->autoDetectPcDevice();
    }

    public function autoDetectPcDevice(): void
    {
        $device = self::findMatchingComputerDevice($this->title, $this->device_identifier, $this->requester_name);

        if ($device) {
            $this->detectedDeviceId = $device->id;
            $this->detectedDevSummary = "{$device->comp_name} — {$device->user_name}";
            $this->detectedDevLocation = $device->location;

            // Otomatis sesuaikan tipe tugas ke preventive jika masih reaktif
            if ($this->task_type === 'reactive') {
                $this->task_type = 'preventive';
            }
            if (empty($this->device_identifier)) {
                $this->device_identifier = $device->comp_name;
            }
            if (empty($this->requester_name)) {
                $this->requester_name = $device->user_name;
            }
            if (empty($this->department_id) && $device->department_id) {
                $this->department_id = $device->department_id;
            }
            if (empty($this->category_id)) {
                $mtcCat = Category::where('code', 'MTC')->orWhere('code', 'PC')->first();
                if ($mtcCat) {
                    $this->category_id = $mtcCat->id;
                }
            }
        } else {
            $this->detectedDeviceId = null;
            $this->detectedDevSummary = null;
            $this->detectedDevLocation = null;
        }
    }

    public static function findMatchingComputerDevice(?string $title, ?string $deviceIdentifier = null, ?string $requesterName = null): ?ComputerDevice
    {
        $combined = trim(($deviceIdentifier ?? '') . ' ' . ($requesterName ?? '') . ' ' . ($title ?? ''));
        if (empty($combined)) {
            return null;
        }

        // 1. Direct match on comp_name or user_name with deviceIdentifier
        if (!empty($deviceIdentifier)) {
            $cleanId = strtolower(trim($deviceIdentifier));
            $dev = ComputerDevice::whereRaw('LOWER(comp_name) = ?', [$cleanId])->first()
                ?? ComputerDevice::whereRaw('LOWER(user_name) = ?', [$cleanId])->first();
            if ($dev) {
                return $dev;
            }

            $namePart = strtolower(trim(preg_replace('/^(pc[-\s]?|laptop[-\s]?|komp[-\s]?)/i', '', $cleanId)));
            $dev = ComputerDevice::whereRaw('LOWER(comp_name) = ?', [$namePart])->first()
                ?? ComputerDevice::whereRaw('LOWER(user_name) = ?', [$namePart])->first();
            if ($dev) {
                return $dev;
            }
        }

        // 2. Direct match on requester_name
        if (!empty($requesterName)) {
            $cleanReq = strtolower(trim($requesterName));
            $dev = ComputerDevice::whereRaw('LOWER(user_name) = ?', [$cleanReq])->first();
            if ($dev) {
                return $dev;
            }
        }

        // 3. Smart multi-word matching across all devices
        $allDevices = ComputerDevice::with('department')->get();

        // Clean search text by removing common boilerplate words
        $cleanText = strtolower($combined);
        $cleanText = preg_replace('/\b(maintenance|perawatan|servis|service|komputer|computer|desktop|laptop|rutin|bulanan|perbaikan|tiket|worklog|cek|pengecekan)\b/i', ' ', $cleanText);

        preg_match_all('/[a-z0-9]+/i', $cleanText, $matches);
        $searchWords = array_values(array_filter($matches[0] ?? [], fn($w) => strlen($w) >= 2));

        if (empty($searchWords)) {
            return null;
        }

        $bestScore = 0;
        $bestDevice = null;

        foreach ($allDevices as $device) {
            $score = 0;
            $dComp = strtolower($device->comp_name);
            $dUser = strtolower($device->user_name);

            // Exact substring matches in combined text
            if (strlen($dComp) >= 2 && str_contains(strtolower($combined), $dComp)) {
                $score += 25;
            }
            if (strlen($dUser) >= 3 && str_contains(strtolower($combined), $dUser)) {
                $score += 35;
            }

            // Word-level matching
            $matchedWordsCount = 0;
            foreach ($searchWords as $w) {
                if ($w === 'pc') continue;

                $matchedInThisDevice = false;
                if (str_contains($dUser, $w)) {
                    $score += 15;
                    $matchedInThisDevice = true;
                }
                if (str_contains($dComp, $w)) {
                    $score += 15;
                    $matchedInThisDevice = true;
                }
                if ($matchedInThisDevice) {
                    $matchedWordsCount++;
                }
            }

            // Bonus for multiple word matches
            if ($matchedWordsCount > 1) {
                $score += ($matchedWordsCount * 10);
            }

            // Disambiguation for "lama" vs "baru" (e.g. Gbaku Lama vs Gbaku Baru)
            if (in_array('lama', $searchWords) && str_contains($dUser, 'lama')) {
                $score += 25;
            }
            if (in_array('lama', $searchWords) && !str_contains($dUser, 'lama') && !str_contains($dComp, 'lama')) {
                $score -= 25;
            }
            if (in_array('baru', $searchWords) && str_contains($dUser, 'lama')) {
                $score -= 25;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestDevice = $device;
            }
        }

        // Return if sufficient confidence threshold reached
        if ($bestScore >= 20) {
            return $bestDevice;
        }

        return null;
    }

    public function openDetailModal(int $id)
    {
        $this->detailLog = WorkLog::with(['category', 'department', 'user', 'attachments', 'sparepartUsages'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openImagePreview(string $url, string $title, ?string $caption = '', string $type = 'after')
    {
        $this->previewImage = [
            'url' => $url,
            'title' => $title,
            'caption' => $caption ?? '',
            'type' => $type,
        ];
        $this->showImageModal = true;
    }

    public function deleteAttachment(int $attachmentId)
    {
        $att = LogAttachment::findOrFail($attachmentId);
        if ($att->file_path && Storage::disk('public')->exists($att->file_path)) {
            Storage::disk('public')->delete($att->file_path);
        }
        $att->delete();

        $this->existingAttachments = array_values(array_filter(
            $this->existingAttachments,
            fn($item) => $item['id'] !== $attachmentId
        ));

        $this->success('Lampiran berhasil dihapus.');
    }

    public function save()
    {
        $this->validate();

        // Calculate duration if both started_at and completed_at exist
        $duration = 0;
        $startCarbon = $this->started_at ? \Carbon\Carbon::parse($this->started_at) : null;
        $endCarbon = $this->completed_at ? \Carbon\Carbon::parse($this->completed_at) : null;

        if ($this->status === 'completed' && !$endCarbon) {
            $endCarbon = now();
            $this->completed_at = $endCarbon->format('Y-m-d');
        }

        if ($startCarbon && $endCarbon) {
            $duration = max(0, $startCarbon->diffInMinutes($endCarbon));
        }

        $data = [
            'title' => $this->title,
            'category_id' => $this->category_id,
            'department_id' => $this->department_id,
            'task_type' => $this->task_type,
            'requester_name' => $this->requester_name ?: 'Internal IT',
            'device_identifier' => $this->device_identifier,
            'priority' => $this->priority,
            'status' => $this->status,
            'started_at' => $this->started_at ? \Carbon\Carbon::parse($this->started_at) : null,
            'completed_at' => $this->completed_at ? \Carbon\Carbon::parse($this->completed_at) : null,
            'duration_minutes' => $duration,
            'description' => $this->description,
            'action_taken' => $this->action_taken,
        ];

        if ($this->workLogId) {
            $log = WorkLog::findOrFail($this->workLogId);
            $log->update($data);
            $this->success('Pekerjaan ' . $log->ticket_number . ' berhasil diperbarui!');
        } else {
            $data['ticket_number'] = $this->ticket_number ?: self::generateTicketNumber();
            $data['user_id'] = auth()->id();
            $log = WorkLog::create($data);
            $this->success('Pekerjaan baru ' . $log->ticket_number . ' berhasil dicatat!');
        }

        // Handle uploaded before photo
        if ($this->beforePhoto) {
            $path = $this->beforePhoto->store('worklog-attachments', 'public');
            $log->attachments()->create([
                'file_path' => $path,
                'original_name' => $this->beforePhoto->getClientOriginalName(),
                'mime_type' => $this->beforePhoto->getMimeType(),
                'attachment_type' => 'before',
                'caption' => $this->beforeCaption ?: 'Foto Sebelum Perbaikan',
            ]);
        }

        // Handle uploaded after photo
        if ($this->afterPhoto) {
            $path = $this->afterPhoto->store('worklog-attachments', 'public');
            $log->attachments()->create([
                'file_path' => $path,
                'original_name' => $this->afterPhoto->getClientOriginalName(),
                'mime_type' => $this->afterPhoto->getMimeType(),
                'attachment_type' => 'after',
                'caption' => $this->afterCaption ?: 'Foto Sesudah Perbaikan',
            ]);
        }

        // Handle uploaded additional attachments
        if (!empty($this->newAttachments)) {
            foreach ($this->newAttachments as $file) {
                $path = $file->store('worklog-attachments', 'public');
                $log->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'attachment_type' => $this->newAttachmentType,
                    'caption' => $this->newAttachmentCaption ?: null,
                ]);
            }
        }

        // Reset file properties after storing - prevents TemporaryUploadedFile
        // serialization issues on subsequent Livewire requests (e.g. clicking X to close)
        $this->beforePhoto = null;
        $this->afterPhoto = null;
        $this->newAttachments = [];
        $this->existingAttachments = [];

        // ─────────────────────────────────────────────────────────────
        // AUTO-LINK ke PC Maintenance
        // Mendeteksi otomatis jika task_type = preventive ATAU
        // judul / deskripsi berkaitan dengan maintenance/perawatan PC
        // ─────────────────────────────────────────────────────────────
        $isMtc = ($this->task_type === 'preventive')
            || preg_match('/\b(maintenance|perawatan|servis|service|mtc)\b/i', $this->title . ' ' . $this->description);

        if ($isMtc) {
            $device = self::findMatchingComputerDevice($this->title, $this->device_identifier, $this->requester_name);

            if ($device) {
                // Pastikan task_type tercatat sebagai preventive
                if ($log->task_type !== 'preventive') {
                    $log->update(['task_type' => 'preventive']);
                }
                // Jika device_identifier masih kosong di log, perbarui dengan comp_name
                if (empty($log->device_identifier)) {
                    $log->update(['device_identifier' => $device->comp_name]);
                }
                if (empty($log->department_id) && $device->department_id) {
                    $log->update(['department_id' => $device->department_id]);
                }

                $period = $log->started_at
                    ? \Carbon\Carbon::parse($log->started_at)->format('Y-m')
                    : date('Y-m');

                PcMaintenanceRecord::updateOrCreate(
                    [
                        'computer_device_id' => $device->id,
                        'period'             => $period,
                    ],
                    [
                        'work_log_id'        => $log->id,
                        'technician_id'      => auth()->id() ?: 1,
                        'maintenance_date'   => $log->started_at
                            ? \Carbon\Carbon::parse($log->started_at)->toDateString()
                            : now()->toDateString(),
                        'notes'              => $log->action_taken ?: ($log->description ?: $log->title),
                        'user_sign_name'     => $this->requester_name ?: $device->user_name,
                        'is_user_signed'     => false,
                        'overall_condition'  => match($this->priority) {
                            'critical' => 'critical',
                            'high'     => 'needs_attention',
                            default    => 'good',
                        },
                    ]
                );

                $locationLabel = $device->location === 'pabrik' ? 'Unit Pabrik' : 'Unit Kantor';
                $this->success(
                    "Work Log disimpan & otomatis tercatat di PC Maintenance untuk perangkat <strong>{$device->comp_name}</strong> ({$device->user_name} &bull; {$locationLabel})!"
                );
            }
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $log = WorkLog::with('attachments')->findOrFail($id);
        foreach ($log->attachments as $att) {
            if ($att->file_path && Storage::disk('public')->exists($att->file_path)) {
                Storage::disk('public')->delete($att->file_path);
            }
        }
        $log->delete();
        $this->success('Pekerjaan berhasil dihapus.');
    }

    public function setDatePreset(string $preset): void
    {
        $this->datePreset = $preset;

        switch ($preset) {
            case 'today':
                $this->startDate = now()->toDateString();
                $this->endDate = now()->toDateString();
                break;
            case 'this_week':
                $this->startDate = now()->startOfWeek()->toDateString();
                $this->endDate = now()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth()->toDateString();
                $this->endDate = now()->endOfMonth()->toDateString();
                break;
            case 'all':
            default:
                $this->datePreset = '';
                $this->startDate = '';
                $this->endDate = '';
                break;
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
            'categoryFilter',
            'departmentFilter',
            'priorityFilter',
            'startDate',
            'endDate',
            'datePreset',
        ]);
        $this->resetPage();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'statusFilter', 'categoryFilter', 'departmentFilter', 'priorityFilter', 'startDate', 'endDate'])) {
            if (in_array($propertyName, ['startDate', 'endDate'])) {
                $this->datePreset = 'custom';
            }
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = WorkLog::with(['user', 'department', 'category', 'attachments'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', $term)
                        ->orWhere('ticket_number', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('requester_name', 'like', $term)
                        ->orWhere('device_identifier', 'like', $term);
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->startDate, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereDate('started_at', '>=', $this->startDate)
                        ->orWhere(function ($s) {
                            $s->whereNull('started_at')
                              ->whereDate('created_at', '>=', $this->startDate);
                        });
                });
            })
            ->when($this->endDate, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereDate('started_at', '<=', $this->endDate)
                        ->orWhere(function ($s) {
                            $s->whereNull('started_at')
                              ->whereDate('created_at', '<=', $this->endDate);
                        });
                });
            })
            ->latest('started_at')
            ->latest('id');

        return view('livewire.work-logs.index', [
            'workLogs' => $query->paginate(10),
            'categories' => Category::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}