<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showModal = false;
    public ?int $supplierId = null;

    // Form fields
    public string $nama_supplier = '';
    public string $alamat = '';
    public string $no_telp = '';
    public string $email = '';
    public string $cp = '';
    public string $no_hp = '';
    public string $keterangan = '';
    public bool $status = true;

    public function updatedSearch() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->reset(['supplierId', 'nama_supplier', 'alamat', 'no_telp', 'email', 'cp', 'no_hp', 'keterangan', 'status']);
        $this->status = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $s = Supplier::findOrFail($id);
        $this->supplierId = $s->id;
        $this->nama_supplier = $s->nama_supplier;
        $this->alamat = $s->alamat ?? '';
        $this->no_telp = $s->no_telp ?? '';
        $this->email = $s->email ?? '';
        $this->cp = $s->cp ?? '';
        $this->no_hp = $s->no_hp ?? '';
        $this->keterangan = $s->keterangan ?? '';
        $this->status = $s->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nama_supplier' => 'required|string|max:100',
            'alamat'        => 'nullable|string|max:150',
            'no_telp'       => 'nullable|string|max:20|unique:m_suppliers,no_telp,' . $this->supplierId,
            'email'         => 'nullable|email|max:100|unique:m_suppliers,email,' . $this->supplierId,
            'cp'            => 'nullable|string|max:100',
            'no_hp'         => 'nullable|string|max:20',
            'keterangan'    => 'nullable|string|max:500',
        ]);

        $data = [
            'nama_supplier' => $this->nama_supplier,
            'alamat'        => $this->alamat ?: null,
            'no_telp'       => $this->no_telp ?: null,
            'email'         => $this->email ?: null,
            'cp'            => $this->cp ?: null,
            'no_hp'         => $this->no_hp ?: null,
            'keterangan'    => $this->keterangan ?: null,
            'status'        => $this->status,
        ];

        if ($this->supplierId) {
            Supplier::findOrFail($this->supplierId)->update($data);
            $this->success('Supplier berhasil diperbarui!');
        } else {
            Supplier::create($data);
            $this->success('Supplier baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $supplier = Supplier::withCount('mtMutasis')->findOrFail($id);
        if ($supplier->mt_mutasis_count > 0) {
            $this->error("Supplier tidak dapat dihapus karena memiliki {$supplier->mt_mutasis_count} mutasi terkait.");
            return;
        }
        $supplier->delete();
        $this->success('Supplier berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';
        $suppliers = Supplier::withCount('mtMutasis')
            ->when($this->search, fn ($q) =>
                $q->where('nama_supplier', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('no_telp', 'like', $term)
                  ->orWhere('cp', 'like', $term)
            )
            ->orderBy('nama_supplier')
            ->paginate(15);

        return view('livewire.suppliers.index', ['suppliers' => $suppliers]);
    }
}
