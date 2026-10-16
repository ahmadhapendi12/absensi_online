<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Karyawan - Lazatto</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 10px;
            line-height: 1.4;
            background-color: #ffffff;
        }
        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #dc2626;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .brand-title {
            font-size: 18px;
            font-weight: 800;
            color: #dc2626;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #374151;
            margin: 2px 0 0 0;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-meta {
            font-size: 9px;
            color: #6b7280;
            text-align: right;
            margin-top: 3px;
        }

        /* Filter Info Box */
        .info-table {
            width: 100%;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 14px;
        }
        .info-label {
            font-weight: 700;
            color: #4b5563;
            width: 15%;
        }
        .info-value {
            color: #111827;
            width: 35%;
        }

        /* Summary Stats Cards */
        .stats-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .stat-card {
            text-align: center;
            padding: 8px 6px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .stat-card.green {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
        }
        .stat-card.yellow {
            background-color: #fffbeb;
            border-color: #fde68a;
        }
        .stat-card.blue {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        .stat-card.red {
            background-color: #fef2f2;
            border-color: #fecaca;
        }
        .stat-number {
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 2px;
        }
        .stat-label {
            font-size: 8px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #1f2937;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8.5px;
            padding: 6px 5px;
            border: 1px solid #1f2937;
            text-align: left;
        }
        .data-table td {
            padding: 5px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 700;
            text-align: center;
        }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
        .badge-purple { background-color: #f3e8ff; color: #6b21a8; }
        .badge-gray { background-color: #f3f4f6; color: #374151; }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-box {
            width: 35%;
            text-align: center;
        }
        .signature-space {
            height: 55px;
        }
        .signature-name {
            font-weight: 700;
            border-top: 1px solid #374151;
            padding-top: 4px;
            display: inline-block;
            min-width: 140px;
        }
        .signature-title {
            font-size: 8.5px;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="brand-title">LAZATTO CHICKEN & BURGER</div>
                <div class="brand-subtitle">Sistem Informasi Presensi & Manajemen SDM Cabang</div>
            </td>
            <td style="vertical-align: middle; text-align: right;">
                <div class="doc-title">Rekapitulasi Presensi</div>
                <div class="doc-meta">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <!-- Filter Metadata Info -->
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
            <td class="info-value">: {{ $labelKaryawan }}</td>
        </tr>
    </table>

    <!-- Summary Statistics KPI Cards -->
    <table class="stats-table">
        <tr>
            <td class="stat-card" style="width: 16%;">
                <div class="stat-number" style="color: #1f2937;">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Entri</div>
            </td>
            <td class="stat-card green" style="width: 16%;">
                <div class="stat-number" style="color: #059669;">{{ $stats['tepat_waktu'] }}</div>
                <div class="stat-label">Tepat Waktu</div>
            </td>
            <td class="stat-card yellow" style="width: 16%;">
                <div class="stat-number" style="color: #d97706;">{{ $stats['terlambat'] }}</div>
                <div class="stat-label">Terlambat</div>
            </td>
            <td class="stat-card blue" style="width: 16%;">
                <div class="stat-number" style="color: #2563eb;">{{ $stats['izin'] + $stats['sakit'] + $stats['cuti'] }}</div>
                <div class="stat-label">Izin / Sakit / Cuti</div>
            </td>
            <td class="stat-card red" style="width: 18%;">
                <div class="stat-number" style="color: #dc2626;">{{ $stats['menit_terlambat'] }} <span style="font-size: 10px;">m</span></div>
                <div class="stat-label">Total Keterlambatan</div>
            </td>
            <td class="stat-card green" style="width: 18%;">
                <div class="stat-number" style="color: #059669;">{{ $stats['persen_kehadiran'] }}%</div>
                <div class="stat-label">Tingkat Disiplin</div>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 10%;">NIK</th>
                <th style="width: 18%;">Nama Karyawan</th>
                <th style="width: 12%;">Cabang</th>
                <th style="width: 11%;">Shift Kerja</th>
                <th style="width: 8%; text-align: center;">Masuk</th>
                <th style="width: 8%; text-align: center;">Pulang</th>
                <th style="width: 8%; text-align: center;">Telat</th>
                <th style="width: 9%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataAbsensi as $i => $item)
            @php
                $isSedangBerlangsung = $item->jam_masuk && !$item->jam_pulang;
                $displayStatus = $isSedangBerlangsung ? 'Sedang Berlangsung' : $item->status_masuk;
                $statusClass = $isSedangBerlangsung ? 'badge-blue' : match($item->status_masuk) {
                    'Tepat Waktu', 'Hadir' => 'badge-green',
                    'Terlambat' => 'badge-yellow',
                    'Izin' => 'badge-blue',
                    'Sakit' => 'badge-purple',
                    'Cuti' => 'badge-gray',
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
                <td style="text-align: center;">{{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : ($item->jam_masuk ? '..' : '-') }}</td>
                <td style="text-align: center;">
                    @if($item->menit_terlambat > 0)
                        <span style="color: #dc2626; font-weight: 700;">+{{ $item->menit_terlambat }} m</span>
                    @else
                        <span style="color: #059669;">-</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <span class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 18px; color: #9ca3af;">
                    Tidak ada data presensi yang ditemukan untuk kriteria filter ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 8.5px; color: #6b7280; font-style: italic;">
                    * Dokumen ini dibuat otomatis oleh Sistem E-Absensi Lazatto dan sah tanpa tanda tangan basah jika diverifikasi secara digital.
                </div>
            </td>
            <td class="signature-box">
                <div class="signature-title">Mengetahui & Menyetujui,</div>
                <div class="signature-space"></div>
                <div class="signature-name">Super Admin / HRD</div>
                <div class="signature-title">Manajemen Operasional Lazatto</div>
            </td>
        </tr>
    </table>

</body>
</html>
