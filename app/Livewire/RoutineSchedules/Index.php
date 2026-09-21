<?php

namespace App\Livewire\RoutineSchedules;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RoutineSchedule;
use App\Models\Category;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showModal = false;
    public ?int $scheduleId = null;

    public string $title = '';
    public string $description = '';
    public string $frequency = 'monthly';
    public ?int $category_id = null;
    public string $target_day = '';
    public bool $is_active = true;

    public function openCreateModal()
    {
        $this->reset(['scheduleId', 'title', 'description', 'target_day']);
        $this->frequency = 'monthly';
        $this->is_active = true;
        $firstCat = Category::first();
        $this->category_id = $firstCat ? $firstCat->id : null;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $item = RoutineSchedule::findOrFail($id);
        $this->scheduleId = $item->id;
        $this->title = $item->title ?? '';
        $this->description = $item->description ?? '';
        $this->frequency = $item->frequency ?? 'monthly';
        $this->category_id = $item->category_id;
        $this->target_day = $item->target_day ?? '';
        $this->is_active = (bool) $item->is_active;
        $this->showModal = true;
    }

    public function toggleActive(int $id)
    {
        $item = RoutineSchedule::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        $this->success("Status jadwal '{$item->title}' berhasil diubah!");
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'frequency' => 'required|string',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'frequency' => $this->frequency,
            'target_day' => $this->target_day,
            'is_active' => $this->is_active,
        ];

        if ($this->scheduleId) {
            RoutineSchedule::findOrFail($this->scheduleId)->update($data);
            $this->success('Jadwal pemeliharaan rutin berhasil diperbarui!');
        } else {
            RoutineSchedule::create($data);
            $this->success('Jadwal pemeliharaan rutin baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        RoutineSchedule::findOrFail($id)->delete();
        $this->success('Jadwal rutin berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';
        $schedules = RoutineSchedule::with('category')
            ->when($this->search, function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term);
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.routine-schedules.index', [
            'schedules' => $schedules,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}