<?php

namespace App\Livewire\ServiceCenters;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ServiceCenter;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showModal = false;
    public ?int $serviceCenterId = null;

    public string $nama_service = '';
    public string $no_telp = '';
    public string $alamat = '';
    public string $cp = '';
    public string $no_hp = '';
    public string $keterangan = '';
    public bool $status = true;

    public function updatedSearch() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->reset(['serviceCenterId', 'nama_service', 'no_telp', 'alamat', 'cp', 'no_hp', 'keterangan', 'status']);
        $this->status = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $sc = ServiceCenter::findOrFail($id);
        $this->serviceCenterId = $sc->id;
        $this->nama_service = $sc->nama_service;
        $this->no_telp = $sc->no_telp ?? '';
        $this->alamat = $sc->alamat ?? '';
        $this->cp = $sc->cp ?? '';
        $this->no_hp = $sc->no_hp ?? '';
        $this->keterangan = $sc->keterangan ?? '';
        $this->status = $sc->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nama_service' => 'required|string|max:100',
            'no_telp'      => 'nullable|string|max:20|unique:m_service_centers,no_telp,' . $this->serviceCenterId,
            'alamat'       => 'nullable|string|max:150',
            'cp'           => 'nullable|string|max:100',
            'no_hp'        => 'nullable|string|max:20',
            'keterangan'   => 'nullable|string|max:500',
        ]);

        $data = [
            'nama_service' => $this->nama_service,
            'no_telp'      => $this->no_telp ?: null,
            'alamat'       => $this->alamat ?: null,
            'cp'           => $this->cp ?: null,
            'no_hp'        => $this->no_hp ?: null,
            'keterangan'   => $this->keterangan ?: null,
            'status'        => $this->status,
        ];

        if ($this->serviceCenterId) {
            ServiceCenter::findOrFail($this->serviceCenterId)->update($data);
            $this->success('Service Center berhasil diperbarui!');
        } else {
            ServiceCenter::create($data);
            $this->success('Service Center baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $sc = ServiceCenter::withCount('services')->findOrFail($id);
        if ($sc->services_count > 0) {
            $this->error("Service Center tidak dapat dihapus karena memiliki {$sc->services_count} data service terkait.");
            return;
        }
        $sc->delete();
        $this->success('Service Center berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';
        $serviceCenters = ServiceCenter::withCount('services')
            ->when($this->search, fn ($q) =>
                $q->where('nama_service', 'like', $term)
                  ->orWhere('alamat', 'like', $term)
                  ->orWhere('no_telp', 'like', $term)
                  ->orWhere('cp', 'like', $term)
            )
            ->orderBy('nama_service')
            ->paginate(15);

        return view('livewire.service-centers.index', ['serviceCenters' => $serviceCenters]);
    }
}
