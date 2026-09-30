<?php

namespace App\Livewire\Mutasis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MtMutasi;
use App\Models\DtMutasi;
use App\Models\Barang;
use App\Models\Pemakai;
use App\Models\Supplier;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $filterJenis = '';
    public string $filterBulan = '';

    // Modal create
    public bool $showModal = false;
    public ?int $mutasiId = null;
    public string $jenis_mutasi = 'perpindahan';
    public string $no_mutasi_preview = '';
    public ?int $m_supplier_id = null;
    public string $tgl_mutasi = '';
    public string $keterangan = '';

    // Detail items (for create)
    public array $details = [];

    // Modal detail (view)
    public bool $showDetailModal = false;
    public ?int $detailMutasiId = null;

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterJenis() { $this->resetPage(); }
    public function updatedFilterBulan() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->reset(['mutasiId', 'jenis_mutasi', 'm_supplier_id', 'tgl_mutasi', 'keterangan', 'details']);
        $this->jenis_mutasi = 'perpindahan';
        $this->tgl_mutasi = now()->format('Y-m-d');
        $this->details = [
            ['m_barang_id' => '', 'pemakai_lama' => 0, 'pemakai_baru' => '', 'harga' => 0],
        ];
        $this->updateNoMutasiPreview();
        $this->showModal = true;
    }

    public function openDetailModal(int $id): void
    {
        $this->detailMutasiId = $id;
        $this->showDetailModal = true;
    }

    public function updatedJenisMutasi()
    {
        $this->updateNoMutasiPreview();
    }

    public function updateNoMutasiPreview()
    {
        $this->no_mutasi_preview = MtMutasi::generateNo($this->jenis_mutasi);
    }

    public function addDetail()
    {
        $this->details[] = ['m_barang_id' => '', 'pemakai_lama' => 0, 'pemakai_baru' => '', 'harga' => 0];
    }

    public function removeDetail(int $index)
    {
        if (count($this->details) > 1) {
            unset($this->details[$index]);
            $this->details = array_values($this->details);
        }
    }

    // When user selects a barang, auto-fill pemakai_lama
    public function updatedDetails($value, $key)
    {
        // key format: "0.m_barang_id"
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'm_barang_id') {
            if ($value) {
                $barang = Barang::find($value);
                if ($barang) {
                    $this->details[$parts[0]]['pemakai_lama'] = $barang->m_pemakai_id ?? 0;
                }
            } else {
                $this->details[$parts[0]]['pemakai_lama'] = 0;
            }
        }
    }

    public function save()
    {
        $rules = [
            'jenis_mutasi'          => 'required|in:pembelian,penjualan,perpindahan',
            'tgl_mutasi'            => 'required|date',
            'm_supplier_id'         => $this->jenis_mutasi === 'pembelian' ? 'required|exists:m_suppliers,id' : 'nullable',
            'keterangan'            => 'nullable|string|max:500',
            'details'               => 'required|array|min:1',
            'details.*.m_barang_id' => 'required|exists:m_barangs,id',
        ];

        if ($this->jenis_mutasi === 'perpindahan') {
            $rules['details.*.pemakai_baru'] = 'required|exists:m_pemakais,id';
        } else {
            $rules['details.*.harga'] = 'nullable|numeric|min:0';
        }

        $this->validate($rules, [
            'details.*.m_barang_id.required'  => 'Semua baris barang harus dipilih.',
            'details.*.m_barang_id.exists'    => 'Barang yang dipilih tidak valid.',
            'details.*.pemakai_baru.required' => 'Pemakai baru harus dipilih untuk perpindahan.',
            'details.*.pemakai_baru.exists'   => 'Pemakai baru tidak valid.',
        ]);

        // Cegah pemilihan barang yang sama di baris berbeda
        $selectedBarangIds = array_filter(array_column($this->details, 'm_barang_id'));
        if (count($selectedBarangIds) !== count(array_unique($selectedBarangIds))) {
            $this->error('Barang yang sama tidak boleh dipilih lebih dari satu kali dalam satu transaksi mutasi!');
            return;
        }

        $noMutasi = MtMutasi::generateNo($this->jenis_mutasi);

        $mt = MtMutasi::create([
            'no_mutasi'     => $noMutasi,
            'm_supplier_id' => ($this->jenis_mutasi === 'pembelian') ? $this->m_supplier_id : null,
            'jenis_mutasi'  => $this->jenis_mutasi,
            'tgl_mutasi'    => $this->tgl_mutasi,
            'keterangan'    => ($this->jenis_mutasi !== 'perpindahan') ? ($this->keterangan ?: null) : null,
        ]);

        foreach ($this->details as $detail) {
            DtMutasi::create([
                'mt_mutasi_id' => $mt->id,
                'm_barang_id'  => $detail['m_barang_id'],
                'pemakai_lama' => $detail['pemakai_lama'] ?: 0,
                'pemakai_baru' => ($this->jenis_mutasi === 'perpindahan') ? ($detail['pemakai_baru'] ?: null) : null,
                'harga'        => ($this->jenis_mutasi === 'perpindahan') ? 0 : ($detail['harga'] ?: 0),
            ]);

            // Side-effects for perpindahan: update barang owner
            if ($this->jenis_mutasi === 'perpindahan' && !empty($detail['pemakai_baru'])) {
                Barang::where('id', $detail['m_barang_id'])
                      ->update(['m_pemakai_id' => $detail['pemakai_baru']]);
            }

            // Side-effects for penjualan: clear barang owner
            if ($this->jenis_mutasi === 'penjualan') {
                Barang::where('id', $detail['m_barang_id'])
                      ->update(['m_pemakai_id' => null, 'status' => 'dijual']);
            }
        }

        $this->success("Mutasi {$noMutasi} berhasil dibuat!");
        $this->showModal = false;
    }

    public function deleteMutasi(int $id)
    {
        $mt = MtMutasi::with('dtMutasis')->findOrFail($id);

        // Rollback side-effects for perpindahan
        if ($mt->jenis_mutasi === 'perpindahan') {
            foreach ($mt->dtMutasis as $dt) {
                if ($dt->pemakai_lama) {
                    Barang::where('id', $dt->m_barang_id)
                          ->update(['m_pemakai_id' => $dt->pemakai_lama]);
                }
            }
        }

        // Rollback for penjualan
        if ($mt->jenis_mutasi === 'penjualan') {
            foreach ($mt->dtMutasis as $dt) {
                Barang::where('id', $dt->m_barang_id)
                      ->update(['status' => 'aktif']);
            }
        }

        $mt->delete(); // Cascade deletes dt_mutasis
        $this->success('Mutasi berhasil dihapus dan efeknya telah di-rollback!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';

        $mutasis = MtMutasi::with(['supplier', 'dtMutasis.barang'])
            ->withCount('dtMutasis')
            ->when($this->search, fn ($q) =>
                $q->where('no_mutasi', 'like', $term)
                  ->orWhere('keterangan', 'like', $term)
                  ->orWhereHas('supplier', fn ($sq) => $sq->where('nama_supplier', 'like', $term))
            )
            ->when($this->filterJenis, fn ($q) =>
                $q->where('jenis_mutasi', $this->filterJenis)
            )
            ->when($this->filterBulan, fn ($q) =>
                $q->whereRaw("DATE_FORMAT(tgl_mutasi, '%Y-%m') = ?", [$this->filterBulan])
            )
            ->orderByDesc('tgl_mutasi')
            ->paginate(15);

        $suppliers = Supplier::where('status', true)->orderBy('nama_supplier')->get();
        $barangs = Barang::with('pemakai')->orderBy('nama_barang')->get();
        $pemakais = Pemakai::with('department')->where('status', true)->orderBy('nama')->get();

        // Barang yang sudah pernah di-mutasi pembelian — dikecualikan saat jenis_mutasi = pembelian
        $barangSudahDibeliIds = $this->jenis_mutasi === 'pembelian'
            ? DtMutasi::whereHas('mtMutasi', fn ($q) => $q->where('jenis_mutasi', 'pembelian'))
                ->pluck('m_barang_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->all()
            : [];

        $barangOptions = $barangs
            ->reject(fn ($b) => in_array((int) $b->id, $barangSudahDibeliIds))
            ->map(function ($b) {
                $snText = $b->serial_number ? "(S/N: {$b->serial_number})" : '(Tanpa S/N)';
                $pemakaiText = $b->pemakai ? " - {$b->pemakai->nama}" : '';
                return [
                    'id'   => $b->id,
                    'name' => "{$b->nama_barang} {$snText}{$pemakaiText}",
                ];
            })->values()->all();

        // Opsi barang per baris: kecualikan barang yang telah dipilih pada baris lain
        $rowBarangOptions = [];
        foreach ($this->details as $idx => $detail) {
            $otherSelectedIds = collect($this->details)
                ->filter(fn ($d, $k) => $k !== $idx && !empty($d['m_barang_id']))
                ->pluck('m_barang_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $rowBarangOptions[$idx] = collect($barangOptions)
                ->reject(fn ($opt) => in_array((int) $opt['id'], $otherSelectedIds))
                ->values()
                ->all();
        }

        $pemakaiOptions = $pemakais->map(function ($p) {
            $deptText = $p->department ? ' (' . ($p->department->code ?: $p->department->name) . ')' : '';
            return [
                'id'   => $p->id,
                'name' => "{$p->nama}{$deptText}",
            ];
        })->values()->all();

        $supplierOptions = $suppliers->map(function ($s) {
            return [
                'id'   => $s->id,
                'name' => $s->nama_supplier,
            ];
        })->values()->all();

        $stats = [
            'total'       => MtMutasi::count(),
            'pembelian'   => MtMutasi::where('jenis_mutasi', 'pembelian')->count(),
            'perpindahan' => MtMutasi::where('jenis_mutasi', 'perpindahan')->count(),
            'penjualan'   => MtMutasi::where('jenis_mutasi', 'penjualan')->count(),
        ];

        // Load data for detail modal
        $detailMutasi = $this->detailMutasiId
            ? MtMutasi::with([
                'supplier',
                'dtMutasis.barang.pemakai',
                'dtMutasis.barang',
            ])->find($this->detailMutasiId)
            : null;

        return view('livewire.mutasis.index', compact('mutasis', 'suppliers', 'barangs', 'pemakais', 'stats', 'barangOptions', 'rowBarangOptions', 'pemakaiOptions', 'supplierOptions', 'detailMutasi'));
    }
}
