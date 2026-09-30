<?php

namespace App\Livewire\Mutasis;

use Livewire\Component;
use App\Models\MtMutasi;
use App\Models\DtMutasi;
use App\Models\Barang;
use App\Models\Pemakai;
use App\Models\Supplier;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public ?int $mutasiId = null;
    public string $jenis_mutasi = 'perpindahan';
    public string $no_mutasi_preview = '';
    public ?int $m_supplier_id = null;
    public string $tgl_mutasi = '';
    public string $keterangan = '';

    public array $details = [];

    public function mount(?int $id = null)
    {
        if ($id) {
            $mt = MtMutasi::with('dtMutasis')->findOrFail($id);
            $this->mutasiId = $mt->id;
            $this->jenis_mutasi = $mt->jenis_mutasi;
            $this->m_supplier_id = $mt->m_supplier_id;
            $this->tgl_mutasi = $mt->tgl_mutasi ? $mt->tgl_mutasi->format('Y-m-d') : now()->format('Y-m-d');
            $this->keterangan = $mt->keterangan ?? '';
            $this->no_mutasi_preview = $mt->no_mutasi;
            $this->details = [];
            foreach ($mt->dtMutasis as $dt) {
                $this->details[] = [
                    'm_barang_id'  => $dt->m_barang_id,
                    'pemakai_lama' => $dt->pemakai_lama ?? 0,
                    'pemakai_baru' => $dt->pemakai_baru,
                    'harga'        => $dt->harga ?? 0,
                ];
            }
        } else {
            $this->jenis_mutasi = 'perpindahan';
            $this->tgl_mutasi = now()->format('Y-m-d');
            $this->details = [
                ['m_barang_id' => '', 'pemakai_lama' => 0, 'pemakai_baru' => '', 'harga' => 0],
            ];
            $this->updateNoMutasiPreview();
        }
    }

    public function updatedJenisMutasi()
    {
        $this->updateNoMutasiPreview();
    }

    public function updateNoMutasiPreview()
    {
        if ($this->mutasiId) {
            $this->no_mutasi_preview = MtMutasi::find($this->mutasiId)?->no_mutasi ?? '';
        } else {
            $this->no_mutasi_preview = MtMutasi::generateNo($this->jenis_mutasi);
        }
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

    public function updatedDetails($value, $key)
    {
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

        if ($this->mutasiId) {
            $mt = MtMutasi::findOrFail($this->mutasiId);
            $mt->update([
                'm_supplier_id' => ($this->jenis_mutasi === 'pembelian') ? $this->m_supplier_id : null,
                'jenis_mutasi'  => $this->jenis_mutasi,
                'tgl_mutasi'    => $this->tgl_mutasi,
                'keterangan'    => ($this->jenis_mutasi !== 'perpindahan') ? ($this->keterangan ?: null) : null,
            ]);
            $mt->dtMutasis()->delete();
        } else {
            $noMutasi = MtMutasi::generateNo($this->jenis_mutasi);
            $mt = MtMutasi::create([
                'no_mutasi'     => $noMutasi,
                'm_supplier_id' => ($this->jenis_mutasi === 'pembelian') ? $this->m_supplier_id : null,
                'jenis_mutasi'  => $this->jenis_mutasi,
                'tgl_mutasi'    => $this->tgl_mutasi,
                'keterangan'    => ($this->jenis_mutasi !== 'perpindahan') ? ($this->keterangan ?: null) : null,
            ]);
        }

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

        session()->flash('mary.toast.title', "Mutasi {$mt->no_mutasi} berhasil disimpan!");
        return redirect()->route('mutasis');
    }

    public function render()
    {
        $suppliers = Supplier::where('status', true)->orderBy('nama_supplier')->get();
        $barangs = Barang::with('pemakai')->orderBy('nama_barang')->get();
        $pemakais = Pemakai::with('department')->where('status', true)->orderBy('nama')->get();

        // Barang yang sudah di-mutasi pembelian — dikecualikan saat jenis_mutasi = pembelian
        // Kecuali barang yang sudah terdaftar di mutasi ini sendiri (saat edit)
        $currentIds = $this->mutasiId
            ? DtMutasi::where('mt_mutasi_id', $this->mutasiId)->pluck('m_barang_id')->map(fn ($id) => (int) $id)->all()
            : [];

        $barangSudahDibeliIds = $this->jenis_mutasi === 'pembelian'
            ? DtMutasi::whereHas('mtMutasi', fn ($q) => $q->where('jenis_mutasi', 'pembelian')
                ->when($this->mutasiId, fn ($q2) => $q2->where('id', '!=', $this->mutasiId)))
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

        return view('livewire.mutasis.form', compact('suppliers', 'barangs', 'pemakais', 'barangOptions', 'rowBarangOptions', 'pemakaiOptions', 'supplierOptions'));
    }
}
