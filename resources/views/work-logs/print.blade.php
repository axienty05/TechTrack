<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Work Log - {{ $user->name }} ({{ $periodLabel }})</title>
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

        /* Data Table */
        table.worklog-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10.5px;
        }

        table.worklog-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #d1d5db;
            font-size: 10px;
            text-transform: uppercase;
        }

        table.worklog-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            line-height: 1.35;
        }

        table.worklog-table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        .ticket-code {
            font-family: monospace;
            font-weight: 700;
            color: #1f2937;
            white-space: nowrap;
        }

        .badge-status {
            display: inline-block;
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
            white-space: nowrap;
        }

        .status-completed { background: #dcfce7; color: #166534; }
        .status-in_progress { background: #dbeafe; color: #1e40af; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-waiting_sparepart { background: #fed7aa; color: #9a3412; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .type-pill {
            display: inline-block;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 1px 5px;
            border-radius: 3px;
            background: #f3f4f6;
            color: #4b5563;
        }

        /* Signatures */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding: 0 40px;
            page-break-inside: avoid;
        }

        .sign-box {
            text-align: center;
            width: 220px;
        }

        .sign-box .sign-role {
            font-size: 11px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 50px;
        }

        .sign-box .sign-name {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
            text-decoration: underline;
        }

        .sign-box .sign-title {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Print Override */
        @media print {
            body {
                background-color: #ffffff;
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
            table.worklog-table th {
                background-color: #f3f4f6 !important;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Toolbar -->
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('work-logs') }}" class="btn btn-secondary">
                &larr; Kembali ke Work Logs
            </a>
            <span style="font-size: 13px; font-weight: 600; color: #4b5563;">
                Pratinjau Laporan: <strong>{{ $user->name }}</strong> ({{ $periodLabel }})
            </span>
        </div>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak / Simpan ke PDF
        </button>
    </div>

    <!-- Paper Container -->
    <div class="print-container">
        <!-- Header / Kop Surat -->
        <div class="header-kop">
            <div class="company-info">
                <h1>PT. ANEKA COFFEE INDUSTRY</h1>
                <h2>DEPARTEMEN INFORMATION TECHNOLOGY (IT)</h2>
                <p>Jl. Raya Surabaya - Malang Km 51, Sukorejo, Pasuruan &bull; Internal IT Log</p>
            </div>
            <div class="doc-title-block">
                <h3>Laporan Aktivitas Pekerjaan</h3>
                <span class="doc-badge">{{ $periodLabel }}</span>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="meta-grid">
            <div class="meta-item">
                <div class="label">Teknisi Pelaksana</div>
                <div class="value">{{ $user->name }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Periode Laporan</div>
                <div class="value">{{ $periodLabel }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Total Pekerjaan</div>
                <div class="value">{{ $stats['total'] }} Tiket ({{ $stats['completed'] }} Selesai)</div>
            </div>
            <div class="meta-item">
                <div class="label">Tanggal Cetak</div>
                <div class="value">{{ now()->translatedFormat('d M Y, H:i') }} WIB</div>
            </div>
        </div>

        <!-- Table -->
        <table class="worklog-table">
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">#</th>
                    <th style="width: 95px;">No. Tiket</th>
                    <th style="width: 100px;">Tanggal & Waktu</th>
                    <th style="width: 110px;">Pemohon / PIC</th>
                    <th style="width: 55px;">Dept</th>
                    <th style="width: 120px;">Kategori IT</th>
                    <th style="width: 75px;">Tipe</th>
                    <th>Judul & Tindakan / Keterangan</th>
                    <th style="width: 85px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workLogs as $index => $log)
                    @php
                        $statusClass = match($log->status) {
                            'completed'         => 'status-completed',
                            'in_progress'       => 'status-in_progress',
                            'pending'           => 'status-pending',
                            'waiting_sparepart' => 'status-waiting_sparepart',
                            'cancelled'         => 'status-cancelled',
                            default             => 'status-in_progress'
                        };
                        $statusLabel = match($log->status) {
                            'completed'         => 'Selesai',
                            'in_progress'       => 'Dalam Proses',
                            'pending'           => 'Tertunda',
                            'waiting_sparepart' => 'Tunggu Part',
                            'cancelled'         => 'Dibatalkan',
                            default             => ucfirst($log->status)
                        };
                        $dateStr = $log->started_at 
                            ? \Carbon\Carbon::parse($log->started_at)->translatedFormat('d/m/Y H:i')
                            : ($log->created_at ? $log->created_at->translatedFormat('d/m/Y H:i') : '-');
                    @endphp
                    <tr>
                        <td style="text-align: center; color: #6b7280;">{{ $index + 1 }}</td>
                        <td>
                            <span class="ticket-code">{{ $log->ticket_number }}</span>
                            @if($log->device_identifier)
                                <div style="font-size: 9px; color: #6b7280; font-family: monospace;">{{ $log->device_identifier }}</div>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">{{ $dateStr }}</td>
                        <td><strong>{{ $log->requester_name ?: '-' }}</strong></td>
                        <td>{{ $log->department->code ?? ($log->department->name ?? '-') }}</td>
                        <td>{{ $log->category->name ?? '-' }}</td>
                        <td>
                            <span class="type-pill">{{ ucfirst($log->task_type) }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #111827; margin-bottom: 2px;">{{ $log->title }}</div>
                            @if($log->action_taken)
                                <div style="font-size: 9.5px; color: #374151;">
                                    <strong>Tindakan:</strong> {{ $log->action_taken }}
                                </div>
                            @elseif($log->description)
                                <div style="font-size: 9.5px; color: #4b5563;">
                                    {{ $log->description }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 24px; color: #9ca3af;">
                            Tidak ada catatan pekerjaan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Lembar Tanda Tangan -->
        <div class="signature-section">
            <div class="sign-box">
                <div class="sign-role">Dibuat Oleh,<br>Teknisi IT</div>
                <div class="sign-name">{{ $user->name }}</div>
                <div class="sign-title">Staff IT Support</div>
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
