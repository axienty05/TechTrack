<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $kategori = '';

    #[Url(history: true)]
    public string $kondisi = '';

    #[Url(history: true)]
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKategori(): void
    {
        $this->resetPage();
    }

    public function updatedKondisi(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'kategori', 'kondisi', 'sortBy']);
        $this->resetPage();
    }

    public function delete(InventoryItem $item): void
    {
        $item->delete();
        $this->success('Barang berhasil dihapus.');
    }

    public function render()
    {
        $items = InventoryItem::query()
            ->with('department')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('nama_barang', 'like', "%{$this->search}%")
                      ->orWhere('kode_barang', 'like', "%{$this->search}%")
                      ->orWhere('merk_model', 'like', "%{$this->search}%")
                      ->orWhere('serial_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->kategori, fn (Builder $query) => $query->where('kategori', $this->kategori))
            ->when($this->kondisi, fn (Builder $query) => $query->where('kondisi', $this->kondisi))
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);

        $headers = [
            ['key' => 'kode_barang', 'label' => 'Kode'],
            ['key' => 'nama_barang', 'label' => 'Nama Barang'],
            ['key' => 'kategori', 'label' => 'Kategori'],
            ['key' => 'kondisi', 'label' => 'Kondisi'],
            ['key' => 'lokasi', 'label' => 'Lokasi'],
            ['key' => 'department.name', 'label' => 'Department'],
        ];

        return view('livewire.inventory.index', [
            'items' => $items,
            'headers' => $headers,
        ]);
    }
}
