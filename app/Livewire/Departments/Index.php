<?php

namespace App\Livewire\Departments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showModal = false;
    public ?int $departmentId = null;

    public string $name = '';
    public string $code = '';
    public string $floor_location = '';

    public function openCreateModal()
    {
        $this->reset(['departmentId', 'name', 'code', 'floor_location']);
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $dep = Department::findOrFail($id);
        $this->departmentId = $dep->id;
        $this->name = $dep->name;
        $this->code = $dep->code ?? '';
        $this->floor_location = $dep->floor_location ?? '';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:departments,code,' . $this->departmentId,
        ]);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'floor_location' => $this->floor_location,
        ];

        if ($this->departmentId) {
            Department::findOrFail($this->departmentId)->update($data);
            $this->success('Departemen berhasil diperbarui!');
        } else {
            Department::create($data);
            $this->success('Departemen baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $dep = Department::withCount('workLogs')->findOrFail($id);
        if ($dep->work_logs_count > 0) {
            $this->error("Departemen tidak dapat dihapus karena memiliki {$dep->work_logs_count} riwayat tiket pekerjaan.");
            return;
        }

        $dep->delete();
        $this->success('Departemen berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';
        $departments = Department::withCount('workLogs')
            ->when($this->search, function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('code', 'like', $term);
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.departments.index', ['departments' => $departments]);
    }
}