<?php

namespace App\Livewire\ServiceInternals;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MServiceInternal;
use App\Models\Barang;
use App\Models\Pemakai;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $filterStatus = ''; // 'selesai', 'proses'
    public string $filterYear = '';

    public bool $showModal = false;
    public ?int $serviceId = null;

    // Print Modal State
    public bool $showPrintModal = false;
    public string $printYear = '';
    public string $printStatus = '';

    // Form fields
    public ?int $m_barang_id = null;
    public ?int $m_pemakai_id = null;
    public string $tgl_service = '';
    public ?string $tgl_selesai = null;
    public string $kerusakan = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterYear() { $this->resetPage(); }

    public function updatedMPemakaiId()
    {
        if (!empty($this->m_pemakai_id)) {
            $userUps = Barang::where('kategori', 'ups')
                ->where('m_pemakai_id', (int) $this->m_pemakai_id)
                ->get();

            // Jika hanya punya 1 UPS, langsung otomatis terpilih
            // Jika ada lebih dari 1 (atau 0), reset agar user memilih sendiri
            if ($userUps->count() === 1) {
                $this->m_barang_id = $userUps->first()->id;
            } else {
                $this->m_barang_id = null;
            }
        } else {
            $this->m_barang_id = null;
        }
    }

    public function openCreateModal()
    {
        $this->reset(['serviceId', 'm_barang_id', 'm_pemakai_id', 'tgl_selesai', 'kerusakan']);
        $this->resetErrorBag();
        $this->tgl_service = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetErrorBag();
        $s = MServiceInternal::findOrFail($id);
        $this->serviceId = $s->id;
        $this->m_barang_id = $s->m_barang_id;
        $this->m_pemakai_id = $s->m_pemakai_id;
        $this->tgl_service = $s->tgl_service ? $s->tgl_service->format('Y-m-d') : '';
        $this->tgl_selesai = $s->tgl_selesai ? $s->tgl_selesai->format('Y-m-d') : null;
        $this->kerusakan = $s->kerusakan ?? '';
        $this->showModal = true;
    }

    public function openPrintModal()
    {
        $this->printYear = $this->filterYear ?: (string) date('Y');
        $this->printStatus = $this->filterStatus;
        $this->showPrintModal = true;
    }

    public function getPrintUrlProperty(): string
    {
        return route('service-internals.print', [
            'year'   => $this->printYear,
            'status' => $this->printStatus,
        ]);
    }

    public function exportExcel()
    {
        $year = $this->printYear;
        $status = $this->printStatus;

        $query = MServiceInternal::with(['barang', 'pemakai.department'])
            ->orderBy('tgl_service', 'asc');

        if (!empty($year)) {
            $query->whereYear('tgl_service', $year);
            $periodLabel = "Tahun_{$year}";
            $titlePeriode = "Tahun {$year}";
        } else {
            $periodLabel = "Semua_Tahun";
            $titlePeriode = "Semua Tahun";
        }

        if ($status === 'selesai') {
            $query->whereNotNull('tgl_selesai');
        } elseif ($status === 'proses') {
            $query->whereNull('tgl_selesai');
        }

        $services = $query->get();

        $filename = "Laporan_Service_Internal_{$periodLabel}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function () use ($services, $titlePeriode) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for MS Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ["LAPORAN SERVICE INTERNAL IT - TECHTRACK"], ",", '"', "\\");
            fputcsv($file, ["Periode: {$titlePeriode}", "Tanggal Unduh: " . now()->format('d/m/Y H:i') . " WIB"], ",", '"', "\\");
            fputcsv($file, [], ",", '"', "\\");

            // Header columns
            fputcsv($file, ['No', 'Tgl Mulai', 'Tgl Selesai', 'Nama Barang', 'Serial Number', 'Kategori', 'Pemakai', 'Departemen', 'Kerusakan', 'Tindakan Perbaikan', 'Status'], ",", '"', "\\");

            foreach ($services as $index => $s) {
                $kerusakanShort = trim(explode('>>', $s->kerusakan)[0]);
                $tindakan = str_contains($s->kerusakan, '>>') 
                    ? trim(substr($s->kerusakan, strpos($s->kerusakan, '>>') + 2)) 
                    : '-';

                fputcsv($file, [
                    $index + 1,
                    $s->tgl_service ? $s->tgl_service->format('d/m/Y') : '-',
                    $s->tgl_selesai ? $s->tgl_selesai->format('d/m/Y') : '-',
                    $s->barang?->nama_barang ?? '-',
                    $s->barang?->serial_number ?? '-',
                    $s->barang?->kategori ?? 'UPS',
                    $s->pemakai?->nama ?? 'Tanpa Pemakai',
                    $s->pemakai?->department?->name ?? '-',
                    $kerusakanShort,
                    $tindakan,
                    $s->tgl_selesai ? 'Selesai' : 'Sedang Dikerjakan',
                ], ",", '"', "\\");
            }

            fclose($file);
        };

        return \Illuminate\Support\Facades\Response::stream($callback, 200, $headers);
    }

    protected function messages(): array
    {
        return [
            'm_barang_id.required'       => 'Barang harus dipilih terlebih dahulu.',
            'm_barang_id.exists'         => 'Barang yang dipilih tidak valid.',
            'm_pemakai_id.exists'        => 'Pemakai yang dipilih tidak valid.',
            'tgl_service.required'       => 'Tanggal mulai pengerjaan wajib diisi.',
            'tgl_service.date'           => 'Format tanggal pengerjaan tidak valid.',
            'tgl_selesai.date'           => 'Format tanggal selesai tidak valid.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai pengerjaan.',
            'kerusakan.required'         => 'Deskripsi kerusakan & tindakan wajib diisi.',
            'kerusakan.max'              => 'Deskripsi kerusakan tidak boleh melebihi 1000 karakter.',
        ];
    }

    public function save()
    {
        $this->validate([
            'm_barang_id'  => 'required|exists:m_barangs,id',
            'm_pemakai_id' => 'nullable|exists:m_pemakais,id',
            'tgl_service'  => 'required|date',
            'tgl_selesai'  => 'nullable|date|after_or_equal:tgl_service',
            'kerusakan'    => 'required|string|max:1000',
        ], $this->messages());

        $data = [
            'm_barang_id'  => $this->m_barang_id,
            'm_pemakai_id' => $this->m_pemakai_id ?: null,
            'tgl_service'  => $this->tgl_service,
            'tgl_selesai'  => $this->tgl_selesai ?: null,
            'kerusakan'    => $this->kerusakan,
        ];

        if ($this->serviceId) {
            MServiceInternal::findOrFail($this->serviceId)->update($data);
            $this->success('Data service internal berhasil diperbarui!');
        } else {
            MServiceInternal::create($data);
            if (empty($this->tgl_selesai)) {
                Barang::where('id', $this->m_barang_id)->update(['status' => 'sedang_service']);
            }
            $this->success('Service internal baru berhasil dicatat!');
        }

        if (!empty($this->tgl_selesai)) {
            Barang::where('id', $this->m_barang_id)->update(['status' => 'aktif']);
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $s = MServiceInternal::findOrFail($id);
        $s->delete();
        $this->success('Data service internal berhasil dihapus!');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterYear']);
        $this->resetPage();
    }

    public function render()
    {
        $term = '%' . $this->search . '%';

        $services = MServiceInternal::with(['barang', 'pemakai.department'])
            ->when($this->search, fn ($q) =>
                $q->where('kerusakan', 'like', $term)
                  ->orWhereHas('barang', fn ($bq) => $bq->where('nama_barang', 'like', $term)
                      ->orWhere('kode_barang', 'like', $term)
                      ->orWhere('serial_number', 'like', $term)
                  )
                  ->orWhereHas('pemakai', fn ($pq) => $pq->where('nama', 'like', $term))
            )
            ->when($this->filterStatus === 'selesai', fn ($q) => $q->whereNotNull('tgl_selesai'))
            ->when($this->filterStatus === 'proses', fn ($q) => $q->whereNull('tgl_selesai'))
            ->when($this->filterYear, fn ($q) => $q->whereYear('tgl_service', $this->filterYear))
            ->orderByDesc('tgl_service')
            ->paginate(15);

        $stats = [
            'total'     => MServiceInternal::count(),
            'proses'    => MServiceInternal::whereNull('tgl_selesai')->count(),
            'selesai'   => MServiceInternal::whereNotNull('tgl_selesai')->count(),
            'total_ups' => Barang::where('kategori', 'ups')->count(),
        ];

        $dbYears = MServiceInternal::whereNotNull('tgl_service')
            ->selectRaw('YEAR(tgl_service) as yr')
            ->distinct()
            ->pluck('yr')
            ->map(fn ($y) => (int) $y)
            ->all();

        $currentYear = (int) date('Y');
        $availableYears = array_unique(array_merge([$currentYear, $currentYear - 1], $dbYears));
        rsort($availableYears);

        $pemakais = Pemakai::where('status', true)->orderBy('nama')->get();

        // Barang: hanya tampilkan UPS milik pemakai yang dipilih (jika ada)
        $barangsQuery = Barang::where('kategori', 'ups')->orderBy('nama_barang');
        if ($this->m_pemakai_id) {
            $barangsQuery->where('m_pemakai_id', $this->m_pemakai_id);
        } else {
            // Jika pemakai belum dipilih, kosongkan list barang
            $barangsQuery->whereRaw('0 = 1');
        }
        $barangs = $barangsQuery->get();

        $pemakaiOptions = $pemakais->map(fn ($p) => [
            'id'   => $p->id,
            'name' => $p->nama,
        ])->values()->all();

        return view('livewire.service-internals.index', compact('services', 'barangs', 'pemakais', 'pemakaiOptions', 'stats', 'availableYears'));
    }
}
