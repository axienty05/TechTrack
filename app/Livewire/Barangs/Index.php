<?php

namespace App\Livewire\Barangs;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Barang;
use App\Models\Pemakai;
use App\Models\Department;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Index extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';
    public string $filterKategori = '';
    public string $filterStatus = '';
    public string $filterDepartment = '';

    // Modal form state
    public bool $showModal = false;
    public ?int $barangId = null;

    // History & Lifecycle modal state
    public bool $showHistoryModal = false;
    public ?int $historyBarangId = null;
    public string $historyTab = 'all'; // 'all', 'mutasi', 'service'

    public function openHistoryModal(int $id): void
    {
        $this->historyBarangId = $id;
        $this->historyTab = 'all';
        $this->showHistoryModal = true;
    }

    // Excel Import state
    public bool $showImportModal = false;
    public $importFile = null;
    public ?array $importSummary = null;

    // Form fields
    public string $kode_barang = '';
    public string $nama_barang = '';
    public string $serial_number = '';
    public string $kategori = 'komputer';
    public ?int $m_pemakai_id = null;
    public string $keterangan = '';
    public string $status = 'aktif';

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

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterKategori() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterDepartment() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->reset(['barangId', 'nama_barang', 'serial_number', 'kategori', 'm_pemakai_id', 'keterangan', 'status']);
        $this->kode_barang = Barang::generateKode();
        $this->kategori = 'komputer';
        $this->status = 'aktif';
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $b = Barang::findOrFail($id);
        $this->barangId = $b->id;
        $this->kode_barang = $b->kode_barang;
        $this->nama_barang = $b->nama_barang;
        $this->serial_number = $b->serial_number ?? '';
        $this->kategori = $b->kategori;
        $this->m_pemakai_id = $b->m_pemakai_id;
        $this->keterangan = $b->keterangan ?? '';
        $this->status = $b->status;
        $this->showModal = true;
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
            $this->success('Barang berhasil diperbarui!');
        } else {
            Barang::create($data);
            $this->success('Barang baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $barang = Barang::withCount(['dtMutasis', 'services', 'serviceInternals'])->findOrFail($id);
        $total = $barang->dt_mutasis_count + $barang->services_count + $barang->service_internals_count;
        if ($total > 0) {
            $this->error("Barang tidak dapat dihapus karena memiliki {$total} riwayat mutasi/service terkait.");
            return;
        }
        $barang->delete();
        $this->success('Barang berhasil dihapus!');
    }

    public function openImportModal()
    {
        $this->importFile = null;
        $this->importSummary = null;
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importSummary = null;
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Barang');

        // Headers
        $headers = [
            'A1' => 'Kode Barang',
            'B1' => 'Nama Barang',
            'C1' => 'Kategori',
            'D1' => 'Serial Number',
            'E1' => 'Nama Pemakai',
            'F1' => 'Status',
            'G1' => 'Keterangan',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Header Style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'], // Indigo / Primary
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(26);

        // Contoh Data
        $examples = [
            ['B/000001', 'PC Dell Optiplex 7050 MT Core i7', 'komputer', '8G2P442', 'Budi Santoso', 'aktif', 'RAM 16GB, SSD 512GB'],
            ['', 'Laptop Lenovo ThinkPad T14 Gen 2', 'laptop', 'PF-29X89', 'Siti Aminah', 'aktif', 'Core i5 Gen 11, Layar 14 inch'],
            ['', 'UPS APC Back-UPS 1200VA BVX1200LI', 'ups', '9B2134091', '', 'aktif', 'Unit UPS di Ruang Server'],
            ['', 'Printer HP LaserJet Pro M404dn', 'printer', 'VNC3K9210', 'Ahmad Dani', 'aktif', 'Printer Divisi Akuntansi'],
            ['', 'Monitor LG 24MP400-B 24 Inch', 'monitor', '104NDSK281', '', 'aktif', 'Cadangan di Gudang IT'],
        ];

        $sheet->fromArray($examples, null, 'A2');

        // Auto width for columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_barang.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'importFile.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'importFile.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'importFile.max'      => 'Ukuran file tidak boleh lebih dari 10MB.',
        ]);

        try {
            $filePath = $this->importFile->getRealPath();
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows) || count($rows) < 2) {
                $this->error('File Excel kosong atau tidak memiliki baris data!');
                return;
            }

            // Cari baris header
            $headerRowKey = null;
            $colMap = [];
            foreach ($rows as $rKey => $row) {
                $nonEmpty = array_filter(array_map('trim', $row));
                if (!empty($nonEmpty)) {
                    $headerRowKey = $rKey;
                    foreach ($row as $colLetter => $cellVal) {
                        $cleaned = strtolower(trim((string)$cellVal));
                        $cleaned = str_replace([' ', '_', '-', '/'], '', $cleaned);
                        if (in_array($cleaned, ['kodebarang', 'kode', 'code', 'barcode'])) {
                            $colMap['kode_barang'] = $colLetter;
                        } elseif (in_array($cleaned, ['namabarang', 'nama', 'namaperangkat', 'namatipe', 'deskripsi', 'itemname', 'item'])) {
                            $colMap['nama_barang'] = $colLetter;
                        } elseif (in_array($cleaned, ['kategori', 'category', 'jenis', 'jeniskategori', 'tipe'])) {
                            $colMap['kategori'] = $colLetter;
                        } elseif (in_array($cleaned, ['serialnumber', 'sn', 'nomorseri', 'seri', 'serial'])) {
                            $colMap['serial_number'] = $colLetter;
                        } elseif (in_array($cleaned, ['pemakai', 'namapemakai', 'user', 'pengguna', 'pic'])) {
                            $colMap['pemakai'] = $colLetter;
                        } elseif (in_array($cleaned, ['status', 'kondisi'])) {
                            $colMap['status'] = $colLetter;
                        } elseif (in_array($cleaned, ['keterangan', 'spesifikasi', 'spek', 'catatan', 'notes'])) {
                            $colMap['keterangan'] = $colLetter;
                        }
                    }
                    break;
                }
            }

            // Fallback mapping urutan default kolom jika header tidak terdeteksi
            if (!$headerRowKey || !isset($colMap['nama_barang'])) {
                $colMap = [
                    'kode_barang'   => 'A',
                    'nama_barang'   => 'B',
                    'kategori'      => 'C',
                    'serial_number' => 'D',
                    'pemakai'       => 'E',
                    'status'        => 'F',
                    'keterangan'    => 'G',
                ];
            }

            // Data existing untuk pengecekan duplikat (Keamanan data)
            $existingKodes = Barang::pluck('kode_barang')->map(fn($v) => strtoupper(trim($v)))->flip()->all();
            $existingSNs = Barang::whereNotNull('serial_number')
                ->where('serial_number', '!=', '')
                ->pluck('serial_number')
                ->map(fn($v) => strtoupper(trim($v)))
                ->flip()
                ->all();

            // Hitung nomor tertinggi untuk format auto-generate B/XXXXXX
            $highestNumber = 0;
            foreach (array_keys($existingKodes) as $code) {
                if (preg_match('/^B\/(\d+)$/i', $code, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $highestNumber) {
                        $highestNumber = $num;
                    }
                }
            }

            // Cache data Pemakai
            $pemakaiLookup = Pemakai::all()->keyBy(fn($p) => strtolower(trim($p->nama)));

            $validCategories = [
                'komputer', 'laptop', 'ups', 'printer', 'monitor',
                'mouse', 'keyboard', 'scanner', 'stavolt', 'memory',
                'storage', 'license', 'sparepart', 'cctv', 'lain_lain',
            ];

            $validStatuses = ['aktif', 'tidak_aktif', 'sedang_service', 'rusak', 'dijual'];

            $dataRows = array_slice($rows, $headerRowKey, null, true);
            $toInsert = [];
            $skippedRows = [];
            $totalCount = 0;
            $batchKodes = [];
            $batchSNs = [];

            foreach ($dataRows as $lineNo => $row) {
                $rowTrimmed = array_filter(array_map(fn($v) => trim((string)$v), $row));
                if (empty($rowTrimmed)) {
                    continue;
                }

                $totalCount++;

                $nama = isset($colMap['nama_barang'], $row[$colMap['nama_barang']])
                    ? trim((string)$row[$colMap['nama_barang']]) : '';

                if (empty($nama)) {
                    $skippedRows[] = [
                        'baris' => $lineNo,
                        'alasan' => 'Nama Barang kosong',
                        'data' => '-',
                    ];
                    continue;
                }

                $rawKode = isset($colMap['kode_barang'], $row[$colMap['kode_barang']])
                    ? trim((string)$row[$colMap['kode_barang']]) : '';
                $rawSN = isset($colMap['serial_number'], $row[$colMap['serial_number']])
                    ? trim((string)$row[$colMap['serial_number']]) : '';
                $rawKat = isset($colMap['kategori'], $row[$colMap['kategori']])
                    ? trim((string)$row[$colMap['kategori']]) : '';
                $rawPemakai = isset($colMap['pemakai'], $row[$colMap['pemakai']])
                    ? trim((string)$row[$colMap['pemakai']]) : '';
                $rawStatus = isset($colMap['status'], $row[$colMap['status']])
                    ? trim((string)$row[$colMap['status']]) : '';
                $rawKet = isset($colMap['keterangan'], $row[$colMap['keterangan']])
                    ? trim((string)$row[$colMap['keterangan']]) : '';

                // Cek duplikasi Serial Number (jika terisi)
                if (!empty($rawSN)) {
                    $snUpper = strtoupper($rawSN);
                    if (isset($existingSNs[$snUpper]) || isset($batchSNs[$snUpper])) {
                        $skippedRows[] = [
                            'baris' => $lineNo,
                            'alasan' => "Serial Number '{$rawSN}' sudah ada di sistem (dilewati)",
                            'data' => $nama,
                        ];
                        continue;
                    }
                }

                // Cek atau buat Kode Barang
                $finalKode = '';
                if (!empty($rawKode)) {
                    $kodeUpper = strtoupper($rawKode);
                    if (isset($existingKodes[$kodeUpper]) || isset($batchKodes[$kodeUpper])) {
                        $skippedRows[] = [
                            'baris' => $lineNo,
                            'alasan' => "Kode Barang '{$rawKode}' sudah ada di sistem (dilewati)",
                            'data' => $nama,
                        ];
                        continue;
                    }
                    $finalKode = $rawKode;
                    if (preg_match('/^B\/(\d+)$/i', $rawKode, $m)) {
                        $highestNumber = max($highestNumber, (int)$m[1]);
                    }
                } else {
                    $highestNumber++;
                    $finalKode = 'B/' . str_pad($highestNumber, 6, '0', STR_PAD_LEFT);
                }

                // Normalisasi Kategori
                $normKat = strtolower(str_replace([' ', '-'], '_', $rawKat));
                if (!in_array($normKat, $validCategories)) {
                    if (str_contains($normKat, 'komputer') || str_contains($normKat, 'pc')) $normKat = 'komputer';
                    elseif (str_contains($normKat, 'laptop') || str_contains($normKat, 'notebook')) $normKat = 'laptop';
                    elseif (str_contains($normKat, 'ups')) $normKat = 'ups';
                    elseif (str_contains($normKat, 'print')) $normKat = 'printer';
                    elseif (str_contains($normKat, 'monitor') || str_contains($normKat, 'layar')) $normKat = 'monitor';
                    elseif (str_contains($normKat, 'mouse')) $normKat = 'mouse';
                    elseif (str_contains($normKat, 'keyboard')) $normKat = 'keyboard';
                    elseif (str_contains($normKat, 'scan')) $normKat = 'scanner';
                    elseif (str_contains($normKat, 'cctv') || str_contains($normKat, 'kamera')) $normKat = 'cctv';
                    elseif (str_contains($normKat, 'ram') || str_contains($normKat, 'memory')) $normKat = 'memory';
                    elseif (str_contains($normKat, 'harddisk') || str_contains($normKat, 'ssd') || str_contains($normKat, 'storage')) $normKat = 'storage';
                    else $normKat = 'komputer';
                }

                // Normalisasi Status
                $normStatus = strtolower(str_replace([' ', '-'], '_', $rawStatus));
                if (!in_array($normStatus, $validStatuses)) {
                    $normStatus = 'aktif';
                }

                // Pemakai lookup
                $mPemakaiId = null;
                if (!empty($rawPemakai)) {
                    $searchPemakai = strtolower($rawPemakai);
                    if (isset($pemakaiLookup[$searchPemakai])) {
                        $mPemakaiId = $pemakaiLookup[$searchPemakai]->id;
                    }
                }

                $record = [
                    'kode_barang'   => $finalKode,
                    'nama_barang'   => substr($nama, 0, 150),
                    'serial_number' => !empty($rawSN) ? substr($rawSN, 0, 50) : null,
                    'kategori'      => $normKat,
                    'm_pemakai_id'  => $mPemakaiId,
                    'status'        => $normStatus,
                    'keterangan'    => !empty($rawKet) ? $rawKet : null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];

                $toInsert[] = $record;
                $batchKodes[strtoupper($finalKode)] = true;
                if (!empty($rawSN)) {
                    $batchSNs[strtoupper($rawSN)] = true;
                }
            }

            // Insert transaksi aman (tanpa menyentuh atau menghapus data yang sudah ada)
            if (!empty($toInsert)) {
                DB::transaction(function () use ($toInsert) {
                    foreach (array_chunk($toInsert, 100) as $chunk) {
                        Barang::insert($chunk);
                    }
                });
            }

            $this->importSummary = [
                'total'           => $totalCount,
                'success'         => count($toInsert),
                'skipped'         => count($skippedRows),
                'skipped_details' => array_slice($skippedRows, 0, 15),
            ];

            if (count($toInsert) > 0) {
                $this->success("Berhasil mengimpor " . count($toInsert) . " data barang baru!");
            } else {
                $this->warning("Tidak ada data baru yang diimpor. Semua baris duplikat atau tidak valid.");
            }

            $this->resetPage();
            $this->importFile = null;

        } catch (\Exception $e) {
            $this->error('Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    public function getHistoryDataProperty(): ?array
    {
        if (!$this->historyBarangId) {
            return null;
        }

        $barang = Barang::with([
            'pemakai.department',
            'dtMutasis.mtMutasi.supplier',
            'services.serviceCenter',
            'services.pemakai',
            'serviceInternals.pemakai',
            'pcMaintenances.technician',
        ])->find($this->historyBarangId);

        if (!$barang) {
            return null;
        }

        $events = [];

        // 1. Mutasi (Pembelian, Perpindahan, Penjualan)
        foreach ($barang->dtMutasis as $dt) {
            $mt = $dt->mtMutasi;
            if (!$mt) continue;

            $date = $mt->tgl_mutasi ? \Carbon\Carbon::parse($mt->tgl_mutasi) : $mt->created_at;

            if ($mt->jenis_mutasi === 'pembelian') {
                $events[] = [
                    'date'        => $date,
                    'type'        => 'pembelian',
                    'category'    => 'mutasi',
                    'title'       => 'Pembelian / Pengadaan Aset',
                    'subtitle'    => 'No. Mutasi: ' . $mt->no_mutasi,
                    'no_mutasi'   => $mt->no_mutasi,
                    'mutasi_id'   => $mt->id,
                    'link'        => route('mutasis', ['search' => $mt->no_mutasi]),
                    'badge'       => 'Pembelian',
                    'badge_class' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                    'icon'        => 'o-shopping-cart',
                    'icon_color'  => 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20',
                    'details'     => [
                        'Supplier / Vendor' => $mt->supplier?->nama_supplier ?? 'Tanpa Supplier',
                        'Harga Beli'        => $dt->harga ? 'Rp ' . number_format($dt->harga, 0, ',', '.') : 'Rp 0',
                        'Keterangan'        => $mt->keterangan ?: '-',
                    ],
                ];
            } elseif ($mt->jenis_mutasi === 'perpindahan') {
                $pemakaiLama = \App\Models\Pemakai::find($dt->pemakai_lama)?->nama ?? 'Gudang / Tanpa Pemakai';
                $pemakaiBaru = \App\Models\Pemakai::find($dt->pemakai_baru)?->nama ?? 'Tanpa Pemakai';

                $events[] = [
                    'date'        => $date,
                    'type'        => 'perpindahan',
                    'category'    => 'mutasi',
                    'title'       => 'Perpindahan Pemakai',
                    'subtitle'    => 'No. Mutasi: ' . $mt->no_mutasi,
                    'no_mutasi'   => $mt->no_mutasi,
                    'mutasi_id'   => $mt->id,
                    'link'        => route('mutasis', ['search' => $mt->no_mutasi]),
                    'badge'       => 'Perpindahan',
                    'badge_class' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                    'icon'        => 'o-arrows-right-left',
                    'icon_color'  => 'text-indigo-500 bg-indigo-500/10 border-indigo-500/20',
                    'details'     => [
                        'Dari Pemakai'    => $pemakaiLama,
                        'Ke Pemakai Baru' => $pemakaiBaru,
                        'Keterangan'      => $mt->keterangan ?: '-',
                    ],
                ];
            } elseif ($mt->jenis_mutasi === 'penjualan') {
                $events[] = [
                    'date'        => $date,
                    'type'        => 'penjualan',
                    'category'    => 'mutasi',
                    'title'       => 'Penjualan / Disposal Aset',
                    'subtitle'    => 'No. Mutasi: ' . $mt->no_mutasi,
                    'no_mutasi'   => $mt->no_mutasi,
                    'mutasi_id'   => $mt->id,
                    'link'        => route('mutasis', ['search' => $mt->no_mutasi]),
                    'badge'       => 'Penjualan',
                    'badge_class' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                    'icon'        => 'o-banknotes',
                    'icon_color'  => 'text-rose-500 bg-rose-500/10 border-rose-500/20',
                    'details'     => [
                        'Harga Jual' => $dt->harga ? 'Rp ' . number_format($dt->harga, 0, ',', '.') : 'Rp 0',
                        'Keterangan' => $mt->keterangan ?: 'Pelepasan aset IT',
                    ],
                ];
            }
        }

        // 2. Service Internal IT
        foreach ($barang->serviceInternals as $si) {
            $date = $si->tgl_service ? \Carbon\Carbon::parse($si->tgl_service) : $si->created_at;
            $events[] = [
                'date'        => $date,
                'type'        => 'service_internal',
                'category'    => 'service',
                'title'       => 'Service Internal IT',
                'subtitle'    => 'Pengerjaan Tim Internal IT',
                'badge'       => 'Service Internal',
                'badge_class' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                'icon'        => 'o-wrench-screwdriver',
                'icon_color'  => 'text-amber-500 bg-amber-500/10 border-amber-500/20',
                'details'     => [
                    'Pemakai Saat Service' => $si->pemakai?->nama ?? '-',
                    'Tgl Mulai'            => $si->tgl_service ? $si->tgl_service->format('d/m/Y') : '-',
                    'Tgl Selesai'          => $si->tgl_selesai ? $si->tgl_selesai->format('d/m/Y') : 'Sedang dalam pengerjaan',
                    'Deskripsi Kerusakan'  => $si->kerusakan ?: '-',
                ],
            ];
        }

        // 3. Service Eksternal (Vendor)
        foreach ($barang->services as $se) {
            $date = $se->tgl_service ? \Carbon\Carbon::parse($se->tgl_service) : $se->created_at;
            $events[] = [
                'date'        => $date,
                'type'        => 'service_eksternal',
                'category'    => 'service',
                'title'       => 'Service Eksternal Vendor',
                'subtitle'    => 'Surat Jalan: ' . ($se->no_sj ?: '-'),
                'badge'       => 'Service Eksternal',
                'badge_class' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                'icon'        => 'o-building-storefront',
                'icon_color'  => 'text-purple-500 bg-purple-500/10 border-purple-500/20',
                'details'     => [
                    'Vendor / Service Center' => $se->serviceCenter?->nama_service ?? '-',
                    'Pemakai'                 => $se->pemakai?->nama ?? '-',
                    'Biaya Perbaikan'         => $se->biaya ? 'Rp ' . number_format($se->biaya, 0, ',', '.') : 'Rp 0',
                    'Gejala Kerusakan'        => $se->kerusakan ?: '-',
                    'Tgl Kirim'               => $se->tgl_service ? $se->tgl_service->format('d/m/Y') : '-',
                    'Tgl Kembali'             => $se->tgl_selesai ? $se->tgl_selesai->format('d/m/Y') : 'Masih di vendor',
                ],
            ];
        }

        // 4. PC Maintenance Rutin (jika ada)
        foreach ($barang->pcMaintenances as $pm) {
            $date = $pm->maintenance_date ? \Carbon\Carbon::parse($pm->maintenance_date) : $pm->created_at;
            $events[] = [
                'date'        => $date,
                'type'        => 'pc_maintenance',
                'category'    => 'service',
                'title'       => 'Maintenance PC Rutin',
                'subtitle'    => 'Periode: ' . ($pm->period ?? '-'),
                'badge'       => 'PC Maintenance',
                'badge_class' => 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-500/20',
                'icon'        => 'o-computer-desktop',
                'icon_color'  => 'text-cyan-500 bg-cyan-500/10 border-cyan-500/20',
                'details'     => [
                    'Teknisi'          => $pm->technician?->name ?? 'Tim IT',
                    'Kondisi Hardware' => $pm->hardware_status ?? 'OK',
                    'Kondisi Software' => $pm->software_status ?? 'OK',
                    'Catatan'          => $pm->notes ?: '-',
                ],
            ];
        }

        // Urutkan dari yang terbaru ke terlama
        usort($events, fn ($a, $b) => $b['date']->timestamp <=> $a['date']->timestamp);

        // Cari mutasi pembelian khusus
        $pembelianEvent = collect($events)->firstWhere('type', 'pembelian');
        $totalPerpindahan = collect($events)->where('type', 'perpindahan')->count();
        $totalService = collect($events)->whereIn('type', ['service_internal', 'service_eksternal'])->count();

        // Filter berdasarkan tab
        $filteredEvents = match ($this->historyTab) {
            'mutasi'  => array_values(array_filter($events, fn($e) => $e['category'] === 'mutasi')),
            'service' => array_values(array_filter($events, fn($e) => $e['category'] === 'service')),
            default   => $events,
        };

        return [
            'barang'           => $barang,
            'events'           => $filteredEvents,
            'totalEvents'      => count($events),
            'pembelian'        => $pembelianEvent,
            'totalPerpindahan' => $totalPerpindahan,
            'totalService'     => $totalService,
        ];
    }

    public function render()
    {
        $term = '%' . $this->search . '%';

        $barangs = Barang::with(['pemakai.department'])
            ->when($this->search, fn ($q) =>
                $q->where('kode_barang', 'like', $term)
                  ->orWhere('nama_barang', 'like', $term)
                  ->orWhere('serial_number', 'like', $term)
                  ->orWhereHas('pemakai', fn ($pq) => $pq->where('nama', 'like', $term))
            )
            ->when($this->filterKategori, fn ($q) =>
                $q->where('kategori', $this->filterKategori)
            )
            ->when($this->filterStatus, fn ($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->when($this->filterDepartment, fn ($q) =>
                $q->whereHas('pemakai', fn ($pq) => $pq->where('department_id', $this->filterDepartment))
            )
            ->orderBy('kode_barang')
            ->paginate(20);

        $pemakais = Pemakai::where('status', true)->orderBy('nama')->get();
        $departments = Department::orderBy('name')->get();

        $kategoriList = [
            'komputer', 'laptop', 'ups', 'printer', 'monitor',
            'mouse', 'keyboard', 'scanner', 'stavolt', 'memory',
            'storage', 'license', 'sparepart', 'cctv', 'lain_lain',
        ];

        $kategoriOptions = collect($kategoriList)->map(fn($k) => [
            'id'   => $k,
            'name' => ucfirst(str_replace('_', ' ', $k)),
        ])->values()->all();

        $pemakaiOptions = Pemakai::with('department')
            ->where('status', true)
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'   => $p->id,
                'name' => $p->nama . ($p->department ? ' (' . ($p->department->code ?: $p->department->name) . ')' : ''),
            ])->values()->all();

        $statusList = ['aktif', 'tidak_aktif', 'sedang_service', 'rusak', 'dijual'];

        $stats = [
            'total'          => Barang::count(),
            'aktif'          => Barang::where('status', 'aktif')->count(),
            'sedang_service' => Barang::where('status', 'sedang_service')->count(),
            'rusak'          => Barang::whereIn('status', ['rusak', 'tidak_aktif'])->count(),
        ];

        $historyData = $this->historyData;

        return view('livewire.barangs.index', compact('barangs', 'pemakais', 'departments', 'kategoriList', 'statusList', 'stats', 'kategoriOptions', 'pemakaiOptions', 'historyData'));
    }
}
