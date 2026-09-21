<?php

namespace App\Livewire\WorkLogs;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\WorkLog;
use App\Models\Category;
use App\Models\Department;
use App\Models\LogAttachment;
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
            'newAttachments', 'newAttachmentCaption', 'existingAttachments'
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

        $this->showModal = true;
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
            ->latest('started_at')
            ->latest('id');

        return view('livewire.work-logs.index', [
            'workLogs' => $query->paginate(10),
            'categories' => Category::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}