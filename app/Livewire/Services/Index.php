<?php

namespace App\Livewire\Services;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MService;
use App\Models\Barang;
use App\Models\Pemakai;
use App\Models\ServiceCenter;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $filterStatus = ''; // 'selesai', 'proses'

    public bool $showModal = false;
    public ?int $serviceId = null;

    // Form fields
    public string $no_sj = '';
    public ?int $m_barang_id = null;
    public ?int $m_pemakai_id = null;
    public ?int $m_service_center_id = null;
    public string $tgl_service = '';
    public ?string $tgl_selesai = null;
    public ?int $biaya = null;
    public string $kerusakan = '';
    public string $analisa = '';
    public string $solusi = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }

    public function updatedMBarangId($value)
    {
        if ($value) {
            $b = Barang::find($value);
            if ($b && $b->m_pemakai_id) {
                $this->m_pemakai_id = $b->m_pemakai_id;
            }
        }
    }

    public function openCreateModal()
    {
        $this->reset(['serviceId', 'no_sj', 'm_barang_id', 'm_pemakai_id', 'm_service_center_id', 'tgl_selesai', 'biaya', 'kerusakan', 'analisa', 'solusi']);
        $this->tgl_service = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $s = MService::findOrFail($id);
        $this->serviceId = $s->id;
        $this->no_sj = $s->no_sj ?? '';
        $this->m_barang_id = $s->m_barang_id;
        $this->m_pemakai_id = $s->m_pemakai_id;
        $this->m_service_center_id = $s->m_service_center_id;
        $this->tgl_service = $s->tgl_service ? $s->tgl_service->format('Y-m-d') : '';
        $this->tgl_selesai = $s->tgl_selesai ? $s->tgl_selesai->format('Y-m-d') : null;
        $this->biaya = $s->biaya;
        $this->kerusakan = $s->kerusakan ?? '';
        $this->analisa = $s->analisa ?? '';
        $this->solusi = $s->solusi ?? '';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'no_sj'               => 'nullable|string|max:50',
            'm_barang_id'         => 'required|exists:m_barangs,id',
            'm_pemakai_id'        => 'nullable|exists:m_pemakais,id',
            'm_service_center_id' => 'required|exists:m_service_centers,id',
            'tgl_service'         => 'required|date',
            'tgl_selesai'         => 'nullable|date|after_or_equal:tgl_service',
            'biaya'               => 'nullable|numeric|min:0',
            'kerusakan'           => 'required|string|max:1000',
            'analisa'             => 'nullable|string|max:1000',
            'solusi'              => 'nullable|string|max:1000',
        ]);

        $data = [
            'no_sj'               => $this->no_sj ?: null,
            'm_barang_id'         => $this->m_barang_id,
            'm_pemakai_id'        => $this->m_pemakai_id ?: null,
            'm_service_center_id' => $this->m_service_center_id,
            'tgl_service'         => $this->tgl_service,
            'tgl_selesai'         => $this->tgl_selesai ?: null,
            'biaya'               => $this->biaya ?: null,
            'kerusakan'           => $this->kerusakan,
            'analisa'             => $this->analisa ?: null,
            'solusi'              => $this->solusi ?: null,
        ];

        if ($this->serviceId) {
            MService::findOrFail($this->serviceId)->update($data);
            $this->success('Data service eksternal berhasil diperbarui!');
        } else {
            MService::create($data);
            // Update status barang ke sedang_service jika belum selesai
            if (empty($this->tgl_selesai)) {
                Barang::where('id', $this->m_barang_id)->update(['status' => 'sedang_service']);
            }
            $this->success('Service eksternal baru berhasil dicatat!');
        }

        // Jika sudah diisi tgl_selesai, kembalikan status barang ke aktif
        if (!empty($this->tgl_selesai)) {
            Barang::where('id', $this->m_barang_id)->update(['status' => 'aktif']);
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $s = MService::findOrFail($id);
        $s->delete();
        $this->success('Data service berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';

        $services = MService::with(['barang', 'pemakai', 'serviceCenter'])
            ->when($this->search, fn ($q) =>
                $q->where('no_sj', 'like', $term)
                  ->orWhere('kerusakan', 'like', $term)
                  ->orWhereHas('barang', fn ($bq) => $bq->where('nama_barang', 'like', $term)->orWhere('kode_barang', 'like', $term))
                  ->orWhereHas('pemakai', fn ($pq) => $pq->where('nama', 'like', $term))
                  ->orWhereHas('serviceCenter', fn ($scq) => $scq->where('nama_service', 'like', $term))
            )
            ->when($this->filterStatus === 'selesai', fn ($q) => $q->whereNotNull('tgl_selesai'))
            ->when($this->filterStatus === 'proses', fn ($q) => $q->whereNull('tgl_selesai'))
            ->orderByDesc('tgl_service')
            ->paginate(15);

        $barangs = Barang::orderBy('kode_barang')->get();
        $pemakais = Pemakai::where('status', true)->orderBy('nama')->get();
        $serviceCenters = ServiceCenter::where('status', true)->orderBy('nama_service')->get();

        return view('livewire.services.index', compact('services', 'barangs', 'pemakais', 'serviceCenters'));
    }
}
