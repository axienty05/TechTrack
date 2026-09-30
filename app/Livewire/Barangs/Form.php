<?php

namespace App\Livewire\Barangs;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Pemakai;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public ?int $barangId = null;

    public string $kode_barang = '';
    public string $nama_barang = '';
    public string $serial_number = '';
    public string $kategori = 'komputer';
    public ?int $m_pemakai_id = null;
    public string $keterangan = '';
    public string $status = 'aktif';

    public function mount(?int $id = null)
    {
        if ($id) {
            $barang = Barang::findOrFail($id);
            $this->barangId = $barang->id;
            $this->kode_barang = $barang->kode_barang;
            $this->nama_barang = $barang->nama_barang;
            $this->serial_number = $barang->serial_number ?? '';
            $this->kategori = $barang->kategori;
            $this->m_pemakai_id = $barang->m_pemakai_id;
            $this->keterangan = $barang->keterangan ?? '';
            $this->status = $barang->status;
        } else {
            $this->kode_barang = Barang::generateKode();
        }
    }

    protected function rules()
    {
        return [
            'kode_barang'   => 'required|string|max:10|unique:m_barangs,kode_barang,' . $this->barangId,
            'nama_barang'   => 'required|string|max:150',
            'serial_number' => 'nullable|string|max:50|unique:m_barangs,serial_number,' . $this->barangId,
            'kategori'      => 'required|in:komputer,laptop,ups,printer,monitor,mouse,keyboard,scanner,stavolt,memory,storage,license,sparepart,cctv,lain_lain',
            'm_pemakai_id'  => 'nullable|exists:m_pemakais,id',
            'keterangan'    => 'nullable|string|max:1000',
            'status'        => 'required|in:aktif,tidak_aktif,sedang_service,rusak,dijual',
        ];
    }

    public function save()
    {
        if (empty($this->m_pemakai_id)) {
            $this->m_pemakai_id = null;
        }

        $this->validate();

        $data = [
            'kode_barang'   => $this->kode_barang,
            'nama_barang'   => $this->nama_barang,
            'serial_number' => $this->serial_number ?: null,
            'kategori'      => $this->kategori,
            'm_pemakai_id'  => $this->m_pemakai_id ?: null,
            'keterangan'    => $this->keterangan ?: null,
            'status'        => $this->status,
        ];

        if ($this->barangId) {
            Barang::findOrFail($this->barangId)->update($data);
            session()->flash('mary.toast.title', 'Barang berhasil diperbarui!');
        } else {
            Barang::create($data);
            session()->flash('mary.toast.title', 'Barang baru berhasil ditambahkan!');
        }

        return redirect()->route('barangs');
    }

    public function render()
    {
        $pemakais = Pemakai::with('department')
            ->where('status', true)
            ->orderBy('nama')
            ->get();

        $kategoriRaw = [
            'komputer', 'laptop', 'ups', 'printer', 'monitor',
            'mouse', 'keyboard', 'scanner', 'stavolt', 'memory',
            'storage', 'license', 'sparepart', 'cctv', 'lain_lain',
        ];

        $kategoriOptions = collect($kategoriRaw)->map(fn($k) => [
            'id'   => $k,
            'name' => ucfirst(str_replace('_', ' ', $k)),
        ])->values()->all();

        $pemakaiOptions = $pemakais->map(fn($p) => [
            'id'   => $p->id,
            'name' => $p->nama . ($p->department ? ' (' . ($p->department->code ?: $p->department->name) . ')' : ''),
        ])->values()->all();

        $statusList = ['aktif', 'tidak_aktif', 'sedang_service', 'rusak', 'dijual'];

        return view('livewire.barangs.form', compact('pemakaiOptions', 'kategoriOptions', 'statusList'));
    }
}
