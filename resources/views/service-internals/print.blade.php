<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Service Internal - {{ $periodLabel }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        /* Screen Action Bar */
        .no-print {
            max-width: 1100px;
            margin: 0 auto 16px auto;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        /* Printable Paper Container */
        .print-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px 32px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        /* Kop Dokumen */
        .header-kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .company-info h1 {
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 3px 0;
            letter-spacing: 0.5px;
            color: #111827;
        }

        .company-info h2 {
            font-size: 13px;
            font-weight: 700;
            margin: 0 0 2px 0;
            color: #2563eb;
        }

        .company-info p {
            font-size: 10px;
            color: #6b7280;
            margin: 0;
        }

        .doc-title-block {
            text-align: right;
        }

        .doc-title-block h3 {
            font-size: 15px;
            font-weight: 800;
            margin: 0 0 3px 0;
            color: #111827;
            text-transform: uppercase;
        }

        .doc-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1e40af;
            font-weight: 700;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }

        /* Meta Info Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }

        .meta-item .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .meta-item .value {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }

        /* Table Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }

        table.data-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        table.data-table td {
            padding: 7px 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            color: #1f2937;
        }

        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-selesai {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-proses {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* Signature Section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 28px;
            page-break-inside: avoid;
        }

        .sign-box {
            width: 200px;
            text-align: center;
        }

        .sign-role {
            font-size: 10px;
            font-weight: 700;
            color: #4b5563;
            margin-bottom: 55px;
        }

        .sign-name {
            font-weight: 700;
            font-size: 11px;
            color: #111827;
            border-bottom: 1px solid #111827;
            padding-bottom: 3px;
            margin-bottom: 2px;
        }

        .sign-title {
            font-size: 9.5px;
            color: #6b7280;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar (Hanya tampil di layar) -->
    <div class="no-print">
        <a href="{{ route('service-internals') }}" class="btn btn-secondary">
            &larr; Kembali ke Service Internal
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak / Simpan ke PDF
        </button>
    </div>

    <!-- Paper Container -->
    <div class="print-container">
        <!-- Kop Dokumen -->
        <div class="header-kop">
            <div class="company-info">
                <h1>TECHTRACK - IT ASSET & SERVICE MANAGEMENT</h1>
                <h2>DIVISI TEKNOLOGI INFORMASI (IT)</h2>
                <p>Dokumen Resmi Catatan Perbaikan & Perawatan Perangkat Internal</p>
            </div>
            <div class="doc-title-block">
                <h3>Laporan Service Internal</h3>
                <span class="doc-badge">{{ $periodLabel }}</span>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="meta-grid">
            <div class="meta-item">
                <div class="label">Periode Laporan</div>
                <div class="value">{{ $periodLabel }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Total Perbaikan</div>
                <div class="value">{{ $stats['total'] }} Catatan</div>
            </div>
            <div class="meta-item">
                <div class="label">Status Selesai</div>
                <div class="value" style="color: #15803d;">{{ $stats['selesai'] }} Unit</div>
            </div>
            <div class="meta-item">
                <div class="label">Tanggal Cetak</div>
                <div class="value">{{ date('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Tabel Data -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">No</th>
                    <th style="width: 80px;">Tgl Mulai</th>
                    <th style="width: 80px;">Tgl Selesai</th>
                    <th style="width: 160px;">Barang</th>
                    <th style="width: 130px;">Pemakai</th>
                    <th>Kerusakan & Tindakan Perbaikan</th>
                    <th style="width: 75px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $index => $s)
                    <tr>
                        <td style="text-align: center; color: #6b7280;">{{ $index + 1 }}</td>
                        <td style="white-space: nowrap; font-family: monospace;">
                            {{ $s->tgl_service ? $s->tgl_service->format('d/m/Y') : '-' }}
                        </td>
                        <td style="white-space: nowrap; font-family: monospace;">
                            {{ $s->tgl_selesai ? $s->tgl_selesai->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            <strong>{{ $s->barang?->nama_barang ?? '-' }}</strong>
                            @if($s->barang?->serial_number)
                                <div style="font-size: 8.5px; color: #4b5563; font-family: monospace;">SN: {{ $s->barang->serial_number }}</div>
                            @endif
                        </td>
                        <td>
                            <div><strong>{{ $s->pemakai?->nama ?? 'Tanpa Pemakai' }}</strong></div>
                            @if($s->pemakai?->department)
                                <div style="font-size: 8.5px; color: #6b7280;">{{ $s->pemakai->department->name }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #111827;">
                                {{ trim(explode('>>', $s->kerusakan)[0]) }}
                            </div>
                            @if(str_contains($s->kerusakan, '>>'))
                                <div style="font-size: 9px; color: #4b5563; margin-top: 2px;">
                                    <strong>Tindakan:</strong> {{ trim(substr($s->kerusakan, strpos($s->kerusakan, '>>') + 2)) }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($s->tgl_selesai)
                                <span class="status-badge status-selesai">Selesai</span>
                            @else
                                <span class="status-badge status-proses">Proses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 24px; color: #9ca3af;">
                            Tidak ada catatan service internal pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Lembar Tanda Tangan -->
        <div class="signature-section">
            <div class="sign-box">
                <div class="sign-role">Dibuat Oleh,<br>Teknisi IT</div>
                <div class="sign-name">{{ $user->name ?? 'Staff IT' }}</div>
                <div class="sign-title">IT Support</div>
            </div>
            <div class="sign-box">
                <div class="sign-role">Mengetahui / Disetujui,<br>Supervisor / IT Head</div>
                <div class="sign-name">( ........................................ )</div>
                <div class="sign-title">IT Department Head</div>
            </div>
        </div>
    </div>

</body>
</html>
