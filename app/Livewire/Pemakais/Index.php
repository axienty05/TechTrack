<?php

namespace App\Livewire\Pemakais;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pemakai;
use App\Models\Department;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showModal = false;
    public ?int $pemakaiId = null;

    // Form fields
    public string $nama = '';
    public string $comp_name = '';
    public ?int $department_id = null;
    public bool $status = true;

    // Filters
    public string $filterDepartment = '';
    public string $filterStatus = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['pemakaiId', 'nama', 'comp_name', 'department_id', 'status']);
        $this->status = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $pemakai = Pemakai::findOrFail($id);
        $this->pemakaiId = $pemakai->id;
        $this->nama = $pemakai->nama;
        $this->comp_name = $pemakai->comp_name ?? '';
        $this->department_id = $pemakai->department_id;
        $this->status = $pemakai->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nama'          => 'required|string|max:100',
            'comp_name'     => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $data = [
            'nama'          => $this->nama,
            'comp_name'     => $this->comp_name ?: null,
            'department_id' => $this->department_id ?: null,
            'status'        => $this->status,
        ];

        if ($this->pemakaiId) {
            Pemakai::findOrFail($this->pemakaiId)->update($data);
            $this->success('Pemakai berhasil diperbarui!');
        } else {
            Pemakai::create($data);
            $this->success('Pemakai baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $pemakai = Pemakai::withCount(['barangs', 'computerDevices'])->findOrFail($id);

        $total = $pemakai->barangs_count + $pemakai->computer_devices_count;
        if ($total > 0) {
            $this->error("Pemakai tidak dapat dihapus karena masih memiliki {$pemakai->barangs_count} barang dan {$pemakai->computer_devices_count} perangkat terhubung.");
            return;
        }

        $pemakai->delete();
        $this->success('Pemakai berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';

        $pemakais = Pemakai::with(['department', 'computerDevices'])
            ->withCount('barangs')
            ->when($this->search, fn ($q) =>
                $q->where('nama', 'like', $term)
                  ->orWhere('comp_name', 'like', $term)
                  ->orWhereHas('department', fn ($dq) => $dq->where('name', 'like', $term))
                  ->orWhereHas('computerDevices', fn ($cq) => $cq->where('comp_name', 'like', $term))
            )
            ->when($this->filterDepartment, fn ($q) =>
                $q->where('department_id', $this->filterDepartment)
            )
            ->when($this->filterStatus !== '', fn ($q) =>
                $q->where('status', $this->filterStatus === '1')
            )
            ->orderBy('nama')
            ->paginate(15);

        $departments = Department::orderBy('name')->get();

        $stats = [
            'total'     => Pemakai::count(),
            'aktif'     => Pemakai::where('status', true)->count(),
            'komputer'  => Pemakai::whereNotNull('comp_name')->where('comp_name', '!=', '')->count(),
            'barangs'   => \App\Models\Barang::whereNotNull('m_pemakai_id')->count(),
        ];

        return view('livewire.pemakais.index', [
            'pemakais'    => $pemakais,
            'departments' => $departments,
            'stats'       => $stats,
        ]);
    }
}
