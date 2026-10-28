<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Presensi Karyawan - Lazatto</title>
    <style>
        @page {
            margin: 18px 22px 20px 22px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9px;
            line-height: 1.35;
            background-color: #ffffff;
        }

        /* Header Branding */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #b91c1c;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-logo {
            width: 70px;
            vertical-align: middle;
        }
        .header-logo img {
            max-height: 48px;
            max-width: 68px;
            object-fit: contain;
        }
        .header-title-box {
            vertical-align: middle;
            padding-left: 8px;
        }
        .brand-name {
            font-size: 16px;
            font-weight: 900;
            color: #b91c1c;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.1;
        }
        .brand-tagline {
            font-size: 9.5px;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
        }
        .header-meta-box {
            vertical-align: middle;
            text-align: right;
            width: 38%;
        }
        .doc-badge {
            display: inline-block;
            background-color: #fee2e2;
            color: #991b1b;
            font-size: 7.5px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            border: 1px solid #fecaca;
        }
        .doc-title {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0;
        }
        .doc-meta {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Filter Info Card */
        .info-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #ea580c;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 4px;
            font-size: 8.5px;
        }
        .info-label {
            font-weight: 700;
            color: #475569;
            width: 14%;
        }
        .info-value {
            color: #0f172a;
            width: 36%;
        }

        /* Summary Stats Cards */
        .stats-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: separate;
            border-spacing: 5px 0;
        }
        .stat-card {
            text-align: center;
            padding: 6px 4px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }
        .stat-card.green {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
            border-top: 3px solid #16a34a;
        }
        .stat-card.yellow {
            background-color: #fefce8;
            border-color: #fef08a;
            border-top: 3px solid #ca8a04;
        }
        .stat-card.blue {
            background-color: #eff6ff;
            border-color: #bfdbfe;
            border-top: 3px solid #2563eb;
        }
        .stat-card.red {
            background-color: #fef2f2;
            border-color: #fecaca;
            border-top: 3px solid #dc2626;
        }
        .stat-card.dark {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            border-top: 3px solid #334155;
        }
        .stat-number {
            font-size: 14px;
            font-weight: 900;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 7.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 2px;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #b91c1c;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.3px;
            padding: 5px 4px;
            border: 1px solid #991b1b;
            text-align: left;
        }
        .data-table td {
            padding: 4px;
            border: 1px solid #e2e8f0;
            font-size: 8.5px;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: 700;
            text-align: center;
        }
        .badge-green { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-yellow { background-color: #fef9c3; color: #a16207; border: 1px solid #fef08a; }
        .badge-blue { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-red { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-purple { background-color: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-orange { background-color: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
        .badge-gray { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* Signatures Section */
        .signature-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .signature-box {
            width: 32%;
            text-align: center;
        }
        .signature-space {
            height: 48px;
        }
        .signature-name {
            font-weight: 800;
            font-size: 9.5px;
            color: #0f172a;
            border-top: 1px solid #475569;
            padding-top: 4px;
            display: inline-block;
            min-width: 150px;
        }
        .signature-title {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    @php
        // Pastikan logo base64 tersedia
        if (!isset($logoBase64) || empty($logoBase64)) {
            $logoPath = file_exists(public_path('lazatto-logo-sm.png')) 
                ? public_path('lazatto-logo-sm.png') 
                : public_path('lazatto-logo.png');
            $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
        }
    @endphp

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            @if($logoBase64)
            <td class="header-logo">
                <img src="{{ $logoBase64 }}" alt="Logo Lazatto">
            </td>
            @endif
            <td class="header-title-box">
                <div class="brand-name">PT. LAZATTO CHICKEN & BURGER</div>
                <div class="brand-tagline">Sistem Informasi Presensi & Manajemen SDM Seluruh Cabang Outlet</div>
            </td>
            <td class="header-meta-box">
                <div class="doc-badge">DOKUMEN RESMI PERUSAHAAN</div>
                <div class="doc-title">Rekapitulasi Presensi</div>
                <div class="doc-meta">Dicetak pada: <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</strong></div>
            </td>
        </tr>
    </table>

    <!-- Filter Metadata Info -->
    <div class="info-card">
        <table class="info-table">
            <tr>
                <td class="info-label">Periode</td>
                <td class="info-value">: <strong>{{ $labelPeriode }}</strong></td>
                <td class="info-label">Outlet / Cabang</td>
                <td class="info-value">: <strong>{{ $labelCabang }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Filter Status</td>
                <td class="info-value">: {{ $labelStatus }}</td>
                <td class="info-label">Karyawan</td>
                <td class="info-value">: <strong>{{ $labelKaryawan }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Shift Kerja</td>
                <td class="info-value">: {{ $labelShift ?? 'Semua Shift' }}</td>
                <td class="info-label">Total Data</td>
                <td class="info-value">: <strong>{{ $stats['total'] }} rekaman presensi</strong></td>
            </tr>
        </table>
    </div>

    <!-- Summary Statistics KPI Cards -->
    <table class="stats-table">
        <tr>
            <td class="stat-card dark" style="width: 16%;">
                <div class="stat-number" style="color: #334155;">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Presensi</div>
            </td>
            <td class="stat-card green" style="width: 16%;">
                <div class="stat-number" style="color: #16a34a;">{{ $stats['tepat_waktu'] }}</div>
                <div class="stat-label">Tepat Waktu</div>
            </td>
            <td class="stat-card yellow" style="width: 16%;">
                <div class="stat-number" style="color: #ca8a04;">{{ $stats['terlambat'] }}</div>
                <div class="stat-label">Terlambat</div>
            </td>
            <td class="stat-card blue" style="width: 16%;">
                <div class="stat-number" style="color: #2563eb;">{{ $stats['izin'] + $stats['sakit'] + $stats['cuti'] }}</div>
                <div class="stat-label">Izin / Sakit / Cuti</div>
            </td>
            <td class="stat-card red" style="width: 18%;">
                <div class="stat-number" style="color: #dc2626;">{{ $stats['menit_terlambat'] }} <span style="font-size: 9px; font-weight: normal;">menit</span></div>
                <div class="stat-label">Total Keterlambatan</div>
            </td>
            <td class="stat-card green" style="width: 18%;">
                <div class="stat-number" style="color: #16a34a;">{{ $stats['persen_kehadiran'] }}%</div>
                <div class="stat-label">Tingkat Disiplin</div>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 11%;">Tanggal</th>
                <th style="width: 10%;">NIK</th>
                <th style="width: 18%;">Nama Karyawan</th>
                <th style="width: 12%;">Cabang Outlet</th>
                <th style="width: 11%;">Shift Kerja</th>
                <th style="width: 8%; text-align: center;">Masuk</th>
                <th style="width: 9%; text-align: center;">Pulang</th>
                <th style="width: 7%; text-align: center;">Telat</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataAbsensi as $i => $item)
            @php
                $displayStatus = $item->status_display;
                $statusClass = match($displayStatus) {
                    'Tepat Waktu', 'Hadir' => 'badge-green',
                    'Terlambat' => 'badge-yellow',
                    'Sedang Berlangsung' => 'badge-blue',
                    'Tidak Absen Pulang' => 'badge-red',
                    'Izin' => 'badge-blue',
                    'Sakit' => 'badge-purple',
                    'Cuti' => 'badge-orange',
                    'Libur' => 'badge-gray',
                    default => 'badge-gray',
                };
            @endphp
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                <td>{{ $item->user?->profile?->nik ?? $item->user?->nik ?? '-' }}</td>
                <td><strong>{{ $item->user?->name ?? '-' }}</strong></td>
                <td>{{ $item->user?->cabang?->nama_cabang ?? 'Pusat' }}</td>
                <td>{{ $item->jadwal?->shift?->nama_shift ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}</td>
                <td style="text-align: center;">{{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : ($item->status_display === 'Tidak Absen Pulang' ? '(Tidak Pulang)' : ($item->jam_masuk ? '..' : '-')) }}</td>
                <td style="text-align: center;">
                    @if($item->menit_terlambat > 0)
                        <span style="color: #dc2626; font-weight: 800;">+{{ $item->menit_terlambat }} m</span>
                    @else
                        <span style="color: #16a34a; font-weight: 700;">-</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <span class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 16px; color: #64748b;">
                    Tidak ada data presensi yang ditemukan untuk kriteria filter ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <table class="signature-table">
        <tr>
            <td style="width: 65%; vertical-align: top;">
                <div style="font-size: 8px; color: #64748b; line-height: 1.4;">
                    <strong>Catatan Sistem:</strong><br>
                    • Dokumen rekapitulasi presensi ini dihasilkan secara otomatis oleh Sistem E-Absensi Lazatto.<br>
                    • Laporan ini merupakan bukti kehadiran sah internal PT. Lazatto Chicken & Burger.<br>
                    • Data status presensi, keterlambatan, dan jam kerja disinkronkan secara real-time.
                </div>
            </td>
            <td class="signature-box" style="vertical-align: top;">
                <div class="signature-title">Mengetahui & Menyetujui,</div>
                <div class="signature-space"></div>
                <div class="signature-name">Super Admin / HRD</div>
                <div class="signature-title">Manajemen Operasional PT. Lazatto</div>
            </td>
        </tr>
    </table>

</body>
</html>
