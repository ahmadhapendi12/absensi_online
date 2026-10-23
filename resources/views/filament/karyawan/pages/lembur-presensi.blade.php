<x-filament-panels::page>
    <style>
        .presensi-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .presensi-container {
                grid-template-columns: 460px 1fr;
                gap: 2rem;
                align-items: start;
            }
        }
        .presensi-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(156, 163, 175, 0.25);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .presensi-card {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(75, 85, 99, 0.4);
        }
        .camera-viewport {
            position: relative;
            background-color: #000000;
            border-radius: 1rem;
            overflow: hidden;
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            aspect-ratio: 3/4;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        .face-oval-guide {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 210px;
            height: 280px;
            border: 2px dashed #f59e0b;
            border-radius: 50% / 50%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.4);
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 1rem;
            z-index: 5;
            transition: all 0.25s ease-in-out;
        }
        .face-oval-guide.aligned {
            border: 3.5px solid #10b981 !important;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.35), 0 0 20px rgba(16, 185, 129, 0.6) !important;
        }
        .face-oval-guide.tilted {
            border: 2.5px solid #f59e0b !important;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.4) !important;
        }
        .face-oval-guide.unaligned {
            border: 2px dashed #ef4444 !important;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45) !important;
        }
        .face-guide-text {
            background: rgba(0, 0, 0, 0.85);
            color: #f59e0b;
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            letter-spacing: 0.025em;
            transition: all 0.2s;
        }
        .info-item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.625rem 0;
            border-bottom: 1px solid rgba(156, 163, 175, 0.15);
            font-size: 0.8125rem;
        }
        .info-item-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
    </style>

    @if(!$lembur)
        <div class="presensi-card" style="text-align: center; padding: 2.5rem 1.5rem;">
            <div style="width: 56px; height: 56px; border-radius: 9999px; background: rgba(239, 68, 68, 0.15); color: #ef4444; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.5rem 0;">Data Pengajuan Lembur Tidak Ditemukan</h2>
            <p style="font-size: 0.875rem; opacity: 0.8; margin: 0 0 1.5rem 0;">Silakan pilih jadwal lembur yang sudah disetujui dari daftar pengajuan lembur Anda.</p>
            <a href="{{ $lemburUrl }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 700; background-color: #f59e0b; color: #ffffff; text-decoration: none;">
                Kembali ke Daftar Lembur
            </a>
        </div>
    @elseif($lembur->status_pengajuan !== 'Disetujui')
        <div class="presensi-card" style="text-align: center; padding: 2.5rem 1.5rem;">
            <div style="width: 56px; height: 56px; border-radius: 9999px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.5rem 0;">Lembur Belum Disetujui</h2>
            <p style="font-size: 0.875rem; opacity: 0.8; margin: 0 0 1.5rem 0;">Pengajuan lembur ini masih berstatus <strong>{{ $lembur->status_pengajuan }}</strong>. Presensi kamera baru bisa dilakukan setelah disetujui oleh atasan / Admin.</p>
            <a href="{{ $lemburUrl }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 700; background-color: #f59e0b; color: #ffffff; text-decoration: none;">
                Kembali ke Daftar Lembur
            </a>
        </div>
    @elseif($isSudahMulai && $isSudahSelesai)
        <div class="presensi-card" style="text-align: center; padding: 2.5rem 1.5rem; background: rgba(16, 185, 129, 0.06); border-color: rgba(16, 185, 129, 0.3);">
            <div style="width: 64px; height: 64px; border-radius: 9999px; background: rgba(16, 185, 129, 0.15); color: #10b981; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0 0 0.5rem 0; color: #059669;">Sesi Lembur Telah Selesai</h2>
            <p style="font-size: 0.9375rem; margin: 0 0 0.5rem 0;">Presensi masuk dan pulang untuk sesi lembur tanggal <strong>{{ \Carbon\Carbon::parse($lembur->tanggal)->format('d M Y') }}</strong> sudah tercatat lengkap.</p>
            <div style="display: inline-block; background: rgba(16, 185, 129, 0.12); padding: 0.75rem 1.25rem; border-radius: 0.75rem; margin: 1rem 0 1.5rem 0; font-size: 0.875rem;">
                <div>Jam Masuk Lembur: <strong>{{ $lembur->jam_mulai_aktual ?? '-' }} WIB</strong></div>
                <div>Jam Pulang Lembur: <strong>{{ $lembur->jam_selesai_aktual ?? '-' }} WIB</strong></div>
                <div style="margin-top: 0.25rem; color: #047857; font-weight: 700;">Total Durasi: {{ round($lembur->durasi_menit / 60, 1) }} Jam ({{ $lembur->durasi_menit }} Menit)</div>
            </div>
            <div>
                <a href="{{ $lemburUrl }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 700; background-color: #10b981; color: #ffffff; text-decoration: none;">
                    Kembali ke Daftar Lembur
                </a>
            </div>
        </div>
    @else
        <!-- BANNER PERINGATAN JIKA BELUM WAKTUNYA MASUK LEMBUR -->
        @if($isBelumBolehMasuk)
            <div style="margin-bottom: 1.25rem; padding: 1.25rem; border-radius: 1rem; background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(239, 68, 68, 0.1)); border: 1.5px solid #f59e0b; display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 9999px; background: rgba(245, 158, 11, 0.2); color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <strong style="font-size: 0.9375rem; display: block; color: #b45309; margin-bottom: 0.25rem;">
                        Presensi Masuk Lembur Belum Dibuka
                    </strong>
                    <span style="font-size: 0.8125rem; line-height: 1.45; display: block; opacity: 0.9;">
                        @if($isBelumBukaTanggal)
                            Sesi lembur Anda dijadwalkan pada tanggal <strong>{{ \Carbon\Carbon::parse($lembur->tanggal)->translatedFormat('l, d F Y') }}</strong>. Presensi masuk kamera hanya dapat dibuka pada tanggal tersebut.
                        @else
                            Sesi lembur Anda dijadwalkan mulai pukul <strong>{{ $jamMulaiJadwal }} WIB</strong>. Sesuai ketentuan, Anda <strong>wajib melakukan presensi masuk tepat pada pukul {{ $jamMulaiJadwal }} WIB atau setelahnya</strong> (Jam saat ini: {{ \Carbon\Carbon::now()->format('H:i') }} WIB).
                        @endif
                    </span>
                </div>
            </div>
        @elseif($isSudahMulai)
            <div style="margin-bottom: 1.25rem; padding: 1rem 1.25rem; border-radius: 0.75rem; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 9999px; background: rgba(16, 185, 129, 0.2); color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div style="font-size: 0.8125rem; line-height: 1.4;">
                    <strong style="color: #047857;">Sesi Lembur Sedang Berjalan (Mulai: {{ $lembur->jam_mulai_aktual }} WIB)</strong>. 
                    <span style="opacity: 0.85;">Waktu pulang lembur bersifat fleksibel. Anda bebas mengambil foto presensi pulang kapan saja saat tugas lembur selesai.</span>
                </div>
            </div>
        @endif

        <!-- BANNER PERMINTAAN IZIN KAMERA & LOKASI -->
        <div id="persistent-permission-bar" style="display: none; margin-bottom: 1.25rem; padding: 1.25rem; border-radius: 1rem; background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(239, 68, 68, 0.08)); border: 1.5px solid rgba(245, 158, 11, 0.4); box-shadow: 0 4px 15px -3px rgba(245, 158, 11, 0.15);">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.875rem;">
                    <div style="width: 44px; height: 44px; border-radius: 9999px; background: rgba(245, 158, 11, 0.2); color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <strong style="font-size: 0.9375rem; display: block;">Akses Kamera & Lokasi Diperlukan</strong>
                        <span style="font-size: 0.8125rem; opacity: 0.85;">Aplikasi memerlukan izin kamera untuk verifikasi wajah dan GPS untuk verifikasi lokasi kantor.</span>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                            <span id="perm-cam-badge" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                                📷 Kamera: Memeriksa...
                            </span>
                            <span id="perm-gps-badge" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                                📍 GPS: Memeriksa...
                            </span>
                        </div>
                    </div>
                </div>
                <button type="button" id="btn-trigger-all-permissions" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.125rem; border-radius: 0.625rem; font-size: 0.8125rem; font-weight: 700; background: #f59e0b; color: white; border: none; cursor: pointer;">
                    Izinkan Kamera & GPS
                </button>
            </div>
        </div>

        <div class="presensi-container">
            <!-- KOLOM KIRI: KAMERA LIVE BIOMETRIK -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="presensi-card" style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9375rem;">
                            <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            <span>{{ $isSudahMulai ? 'Kamera Pulang Lembur' : 'Kamera Masuk Lembur' }}</span>
                        </div>
                        <span id="camera-status-label" style="font-size: 0.75rem; opacity: 0.8; font-weight: 600;">Menghubungkan Kamera...</span>
                    </div>

                    <div class="camera-viewport" id="viewport-box">
                        <video id="webcam-video" playsinline autoplay muted style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                        <canvas id="face-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; transform: scaleX(-1);"></canvas>
                        
                        <div class="face-oval-guide" id="face-guide">
                            <span class="face-guide-text" id="face-guide-status">Posisikan Wajah</span>
                        </div>

                        <div id="camera-loading-overlay" style="position: absolute; inset: 0; background: #000000; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem; color: #ffffff; z-index: 10;">
                            <div style="width: 36px; height: 36px; border: 3px solid rgba(245, 158, 11, 0.3); border-top-color: #f59e0b; border-radius: 9999px; animation: spin 1s linear infinite;"></div>
                            <span id="camera-loading-text" style="font-size: 0.8125rem; font-weight: 600;">Memuat AI Biometrik & Kamera...</span>
                        </div>
                    </div>

                    <div style="margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; opacity: 0.75;">
                        <span id="gps-status-text">📍 Mendeteksi Lokasi GPS...</span>
                        <span id="face-detection-status">👤 AI Wajah: Menunggu...</span>
                    </div>

                    <div style="margin-top: 1.25rem;">
                        <button 
                            type="button" 
                            id="btn-submit-presensi" 
                            disabled
                            style="width: 100%; padding: 0.875rem 1.5rem; border-radius: 0.75rem; font-weight: 800; font-size: 0.9375rem; background: {{ $isSudahMulai ? '#10b981' : ($isBelumBolehMasuk ? '#9ca3af' : '#f59e0b') }}; color: #ffffff; border: none; cursor: not-allowed; opacity: 0.6; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 12px {{ $isSudahMulai ? 'rgba(16, 185, 129, 0.35)' : 'rgba(245, 158, 11, 0.35)' }}; transition: all 0.2s;"
                        >
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            <span id="btn-submit-text">
                                @if($isSudahMulai)
                                    Ambil Foto & Presensi Pulang Lembur
                                @elseif($isBelumBolehMasuk)
                                    Belum Waktunya (Mulai Pukul {{ $jamMulaiJadwal ?? '10:00' }} WIB)
                                @else
                                    Ambil Foto & Presensi Masuk Lembur
                                @endif
                            </span>
                        </button>
                        <p id="submit-help-text" style="font-size: 0.75rem; text-align: center; margin-top: 0.5rem; opacity: 0.7;">
                            @if($isBelumBolehMasuk)
                                Presensi masuk baru dapat dilakukan saat jam sudah mencapai pukul {{ $jamMulaiJadwal }} WIB atau setelahnya.
                            @elseif($isSudahMulai)
                                Posisikan wajah tegak lurus di lingkaran hijau untuk menyelesaikan sesi lembur.
                            @else
                                Posisikan wajah tegak lurus di lingkaran kuning hingga berubah hijau dan berada di area outlet Lazatto.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: DETAIL PENUGASAN LEMBUR -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="presensi-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(156, 163, 175, 0.2);">
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Rincian Penugasan Lembur</h3>
                            <span style="font-size: 0.8125rem; opacity: 0.75;">{{ \Carbon\Carbon::parse($lembur->tanggal)->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $isSudahMulai ? 'rgba(245, 158, 11, 0.15)' : 'rgba(59, 130, 246, 0.15)' }}; color: {{ $isSudahMulai ? '#d97706' : '#2563eb' }};">
                            {{ $isSudahMulai ? '⏳ Sedang Berjalan' : '🚀 Siap Dimulai' }}
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column;">
                        <div class="info-item-row">
                            <span style="opacity: 0.75;">Nama Karyawan</span>
                            <span style="font-weight: 700;">{{ $user->name }}</span>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.75;">Penempatan Outlet</span>
                            <span style="font-weight: 700;">{{ $cabang?->nama_cabang ?? 'Pusat / Seluruh Outlet' }}</span>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.75;">Status Pengajuan</span>
                            <span style="color: #059669; font-weight: 700;">Disetujui Admin</span>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.75;">Jadwal Mulai Pengajuan</span>
                            <span style="font-weight: 700; color: #f59e0b;">{{ $jamMulaiJadwal ? $jamMulaiJadwal . ' WIB' : '-' }} (Wajib jam ini/setelahnya)</span>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.75;">Uraian Tugas Lembur</span>
                            <span style="font-weight: 600; text-align: right; max-width: 60%;">{{ $lembur->alasan_lembur }}</span>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.75;">Jam Mulai Aktual</span>
                            <span style="font-weight: 700; color: {{ $lembur->foto_mulai ? '#059669' : '#f59e0b' }};">
                                {{ $lembur->foto_mulai ? $lembur->jam_mulai_aktual . ' WIB' : 'Belum Mulai (Akan tercatat otomatis saat jepret masuk)' }}
                            </span>
                        </div>
                        @if($lembur->foto_mulai)
                            <div class="info-item-row">
                                <span style="opacity: 0.75;">Jam Pulang Aktual</span>
                                <span style="font-weight: 700; color: #10b981;">
                                    Bebas Fleksibel (Klik tombol pulang saat selesai)
                                </span>
                            </div>
                        @endif
                    </div>

                    <div style="margin-top: 1.5rem; padding: 1rem; border-radius: 0.75rem; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); font-size: 0.8125rem; line-height: 1.5;">
                        <strong style="display: block; color: #1d4ed8; margin-bottom: 0.25rem;">💡 Aturan Masuk & Pulang Lembur:</strong>
                        <ul style="margin: 0; padding-left: 1.25rem; opacity: 0.85;">
                            <li><strong>Masuk Lembur:</strong> Wajib tepat pada jam mulai jadwal pengajuan (<strong>{{ $jamMulaiJadwal ?? '10:00' }} WIB</strong>) atau setelahnya.</li>
                            <li><strong>Pulang Lembur:</strong> Bersifat <strong>bebas / fleksibel</strong>. Tidak ada batasan kaku jam pulang, ambil foto kepulangan kapan saja saat pekerjaan selesai.</li>
                            <li>Durasi lembur dihitung otomatis dari selisih waktu masuk riil sampai pulang riil.</li>
                        </ul>
                    </div>

                    <div style="margin-top: 1.25rem; text-align: center;">
                        <a href="{{ $lemburUrl }}" style="font-size: 0.8125rem; color: #f59e0b; text-decoration: underline; font-weight: 600;">
                            ← Kembali ke Tabel Daftar Lembur
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Gunakan Script Face-API Lokal yang Sama dengan Presensi Reguler -->
    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        (function() {
            let streamInstance = null;
            let faceTrackingInterval = null;
            let isFaceModelsLoaded = false;
            let isCameraActive = false;
            let currentLat = null;
            let currentLng = null;
            let isFaceAligned = false;

            const targetLat = {{ (float) ($cabang?->latitude ?? -6.2088) }};
            const targetLng = {{ (float) ($cabang?->longitude ?? 106.8456) }};
            const targetRadius = {{ (int) ($cabang?->radius_meter ?? 50) }};
            const lemburId = '{{ $lembur?->id }}';
            const isSudahMulai = {{ $isSudahMulai ? 'true' : 'false' }};
            const isBelumBolehMasuk = {{ $isBelumBolehMasuk ? 'true' : 'false' }};
            const lemburUrl = '{{ $lemburUrl }}';
            const MODEL_URL = '{{ asset('models') }}';

            function getDistanceMeters(lat1, lon1, lat2, lon2) {
                const R = 6371e3;
                const φ1 = lat1 * Math.PI / 180;
                const φ2 = lat2 * Math.PI / 180;
                const Δφ = (lat2 - lat1) * Math.PI / 180;
                const Δλ = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                          Math.cos(φ1) * Math.cos(φ2) *
                          Math.sin(Δλ/2) * Math.sin(Δλ/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            function showAppModal(title, message, iconType, onClose) {
                const overlay = document.getElementById('app-modal-overlay');
                const card = document.getElementById('app-modal-card');
                const titleEl = document.getElementById('app-modal-title');
                const msgEl = document.getElementById('app-modal-message');
                const iconBox = document.getElementById('app-modal-icon-container');
                const btnClose = document.getElementById('app-modal-btn-close');

                if (!overlay) return;

                titleEl.innerText = title;
                msgEl.innerText = message;

                if (iconType === 'success') {
                    iconBox.style.background = '#d1fae5';
                    iconBox.style.color = '#059669';
                    iconBox.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
                    btnClose.style.background = '#10b981';
                } else if (iconType === 'warning') {
                    iconBox.style.background = '#fef3c7';
                    iconBox.style.color = '#d97706';
                    iconBox.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                    btnClose.style.background = '#f59e0b';
                } else {
                    iconBox.style.background = '#fee2e2';
                    iconBox.style.color = '#dc2626';
                    iconBox.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    btnClose.style.background = '#ef4444';
                }

                overlay.style.display = 'flex';
                setTimeout(() => {
                    card.style.transform = 'scale(1)';
                    card.style.opacity = '1';
                }, 20);

                btnClose.onclick = function() {
                    card.style.transform = 'scale(0.9)';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        overlay.style.display = 'none';
                        if (typeof onClose === 'function') onClose();
                    }, 180);
                };
            }

            async function initLemburCamera() {
                if (!lemburId) return;

                const video = document.getElementById('webcam-video');
                const loadingOverlay = document.getElementById('camera-loading-overlay');
                const loadingText = document.getElementById('camera-loading-text');
                const statusLabel = document.getElementById('camera-status-label');
                const btnSubmit = document.getElementById('btn-submit-presensi');
                const guide = document.getElementById('face-guide');
                const guideStatus = document.getElementById('face-guide-status');
                const gpsStatusText = document.getElementById('gps-status-text');
                const faceDetectionStatus = document.getElementById('face-detection-status');

                const persistentPermBar = document.getElementById('persistent-permission-bar');
                const permCamBadge = document.getElementById('perm-cam-badge');
                const permGpsBadge = document.getElementById('perm-gps-badge');
                const btnTriggerPerms = document.getElementById('btn-trigger-all-permissions');

                let isCameraAllowed = false;
                let isGpsAllowed = false;

                function updatePersistentPermissionBar(forceShow = false) {
                    if (!persistentPermBar) return;
                    const isCamLive = Boolean(streamInstance && streamInstance.active);
                    const isGpsLive = Boolean(currentLat !== null && currentLng !== null);

                    if (isCamLive) isCameraAllowed = true;
                    if (isGpsLive) isGpsAllowed = true;

                    if (permCamBadge) {
                        if (isCameraAllowed) {
                            permCamBadge.innerHTML = '📷 Kamera: Diizinkan ✓';
                            permCamBadge.style.background = 'rgba(16, 185, 129, 0.15)';
                            permCamBadge.style.color = '#059669';
                        } else {
                            permCamBadge.innerHTML = '📷 Kamera: Belum Diizinkan ⚠️';
                            permCamBadge.style.background = 'rgba(239, 68, 68, 0.15)';
                            permCamBadge.style.color = '#dc2626';
                        }
                    }

                    if (permGpsBadge) {
                        if (isGpsAllowed) {
                            permGpsBadge.innerHTML = '📍 GPS: Diizinkan ✓';
                            permGpsBadge.style.background = 'rgba(16, 185, 129, 0.15)';
                            permGpsBadge.style.color = '#059669';
                        } else {
                            permGpsBadge.innerHTML = '📍 GPS: Belum Diizinkan ⚠️';
                            permGpsBadge.style.background = 'rgba(239, 68, 68, 0.15)';
                            permGpsBadge.style.color = '#dc2626';
                        }
                    }

                    if (isCameraAllowed && isGpsAllowed) {
                        persistentPermBar.style.display = 'none';
                    } else if (forceShow) {
                        persistentPermBar.style.display = 'block';
                    }
                }

                if (persistentPermBar) persistentPermBar.style.display = 'none';

                if (btnTriggerPerms) {
                    btnTriggerPerms.onclick = function() {
                        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
                                .then(s => {
                                    streamInstance = s;
                                    isCameraAllowed = true;
                                    if (video) { video.srcObject = s; video.play(); }
                                    updatePersistentPermissionBar();
                                })
                                .catch(() => {
                                    updatePersistentPermissionBar(true);
                                    showAppModal('Akses Ditolak', 'Akses kamera atau GPS diblokir oleh peramban. Silakan klik ikon gembok 🔒 di samping URL browser, ubah izin menjadi "Izinkan", lalu refresh.', 'warning');
                                });
                        }
                    };
                }

                // 1. Dapatkan GPS
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        pos => {
                            currentLat = pos.coords.latitude;
                            currentLng = pos.coords.longitude;
                            isGpsAllowed = true;
                            updatePersistentPermissionBar();
                            const dist = getDistanceMeters(currentLat, currentLng, targetLat, targetLng);
                            if (gpsStatusText) {
                                gpsStatusText.innerText = '📍 ' + Math.round(dist) + 'm dari Outlet';
                            }
                        },
                        err => {
                            isGpsAllowed = false;
                            updatePersistentPermissionBar(true);
                            if (gpsStatusText) gpsStatusText.innerText = '📍 GPS Standby (Akurasi Rendah)';
                        },
                        { enableHighAccuracy: true, timeout: 8000 }
                    );
                }

                // 2. Load Face Models (Sama persis dengan Presensi Reguler)
                try {
                    if (loadingText) loadingText.innerText = 'Memuat AI Biometrik Lazatto...';
                    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                    isFaceModelsLoaded = true;
                } catch(e) {
                    console.warn('Gagal memuat model face-api:', e);
                }

                // 3. Start Camera
                try {
                    if (loadingText) loadingText.innerText = 'Menyalakan Kamera...';
                    const constraints = {
                        video: {
                            facingMode: 'user',
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        },
                        audio: false
                    };
                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    streamInstance = stream;
                    if (video) {
                        video.srcObject = stream;
                        await video.play();
                    }
                    isCameraActive = true;
                    isCameraAllowed = true;
                    updatePersistentPermissionBar();
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                    if (statusLabel) statusLabel.innerText = 'Kamera Aktif';
                } catch(err) {
                    isCameraAllowed = false;
                    updatePersistentPermissionBar(true);
                    if (loadingText) loadingText.innerText = 'Kamera Gagal Dibuka. Harap Izinkan Akses.';
                    return;
                }

                // 4. Face Detection Loop dengan Analisis Landmark & Geometris Ketat (Sama Persis dengan Presensi Mandiri)
                faceTrackingInterval = setInterval(async () => {
                    if (!isCameraActive || !video || video.paused || video.ended) return;

                    try {
                        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.70 });
                        const detection = await faceapi.detectSingleFace(video, options).withFaceLandmarks();

                        if (detection && detection.detection.score >= 0.70) {
                            const landmarks = detection.landmarks;
                            const jaw = landmarks.getJawOutline();
                            const nose = landmarks.getNose();
                            const leftEye = landmarks.getLeftEye();
                            const rightEye = landmarks.getRightEye();

                            const videoW = video.videoWidth || 640;
                            const videoH = video.videoHeight || 480;
                            const centerX = videoW / 2;
                            const centerY = videoH / 2;

                            const box = detection.detection.box;
                            const faceX = box.x + box.width / 2;
                            const faceY = box.y + box.height / 2;
                            const diffX = Math.abs(faceX - centerX);
                            const diffY = Math.abs(faceY - centerY);

                            // 1. Validasi Posisi: Wajah harus di tengah panduan oval
                            const isCentered = (diffX <= videoW * 0.18) && (diffY <= videoH * 0.20);

                            // 2. Validasi Jarak: Wajah proporsional (24% - 65% frame)
                            const isProperSize = (box.width >= videoW * 0.24) && (box.width <= videoW * 0.65) && (box.height >= videoH * 0.28);

                            // 3. Validasi Kesimetrisan Hidung & Rahang (menghadap lurus)
                            const noseTip = nose[3] || nose[0];
                            const leftJaw = jaw[2] || jaw[0];
                            const rightJaw = jaw[14] || jaw[16];
                            const distLeft = Math.hypot(noseTip.x - leftJaw.x, noseTip.y - leftJaw.y);
                            const distRight = Math.hypot(rightJaw.x - noseTip.x, rightJaw.y - noseTip.y);
                            const symmetryRatio = distLeft / (distRight || 1);
                            const isFacingFront = (symmetryRatio >= 0.70) && (symmetryRatio <= 1.45);

                            // 4. Validasi Kemiringan Kepala
                            const eyeDeltaY = Math.abs(leftEye[0].y - rightEye[3].y);
                            const eyeDeltaX = Math.abs(leftEye[0].x - rightEye[3].x);
                            const tiltRatio = eyeDeltaY / (eyeDeltaX || 1);
                            const isUpright = (tiltRatio <= 0.22);

                            if (!isProperSize && box.width < videoW * 0.24) {
                                isFaceAligned = false;
                                if (guide) guide.className = 'face-oval-guide tilted';
                                if (guideStatus) { guideStatus.innerText = 'DEKATKAN WAJAH KE KAMERA'; guideStatus.style.color = '#fbbf24'; }
                            } else if (!isProperSize && box.width > videoW * 0.65) {
                                isFaceAligned = false;
                                if (guide) guide.className = 'face-oval-guide tilted';
                                if (guideStatus) { guideStatus.innerText = 'MUNDUR SEDIKIT DARI KAMERA'; guideStatus.style.color = '#fbbf24'; }
                            } else if (!isCentered) {
                                isFaceAligned = false;
                                if (guide) guide.className = 'face-oval-guide unaligned';
                                if (guideStatus) { guideStatus.innerText = 'ARAHKAN WAJAH KE TENGAH OVAL'; guideStatus.style.color = '#ef4444'; }
                            } else if (!isUpright) {
                                isFaceAligned = false;
                                if (guide) guide.className = 'face-oval-guide tilted';
                                if (guideStatus) { guideStatus.innerText = 'TEGAKKAN POSISI KEPALA'; guideStatus.style.color = '#fbbf24'; }
                            } else if (!isFacingFront) {
                                isFaceAligned = false;
                                if (guide) guide.className = 'face-oval-guide tilted';
                                if (guideStatus) { guideStatus.innerText = 'HADAPKAN WAJAH LURUS KE DEPAN'; guideStatus.style.color = '#fbbf24'; }
                            } else {
                                // SEMPURNA: Semua syarat geometris terpenuhi
                                isFaceAligned = true;
                                if (guide) guide.className = 'face-oval-guide aligned';
                                if (guideStatus) { guideStatus.innerText = '✓ POSISI WAJAH SEMPURNA'; guideStatus.style.color = '#10b981'; }
                                if (faceDetectionStatus) { faceDetectionStatus.innerText = '👤 AI: Siap Verifikasi (Skor: ' + Math.round(detection.detection.score * 100) + '%)'; }
                            }

                            // Aktifkan tombol submit hanya jika posisi sempurna DAN sudah waktunya
                            if (btnSubmit) {
                                if (isFaceAligned && !isBelumBolehMasuk) {
                                    btnSubmit.disabled = false;
                                    btnSubmit.style.opacity = '1';
                                    btnSubmit.style.cursor = 'pointer';
                                } else {
                                    btnSubmit.disabled = true;
                                    btnSubmit.style.opacity = '0.6';
                                    btnSubmit.style.cursor = 'not-allowed';
                                }
                            }
                        } else {
                            isFaceAligned = false;
                            if (guide) guide.className = 'face-oval-guide';
                            if (guideStatus) { guideStatus.innerText = 'POSISIKAN WAJAH DI SINI'; guideStatus.style.color = '#f59e0b'; }
                            if (faceDetectionStatus) { faceDetectionStatus.innerText = '👤 AI: Mencari Wajah...'; }
                            if (btnSubmit) {
                                btnSubmit.disabled = true;
                                btnSubmit.style.opacity = '0.6';
                                btnSubmit.style.cursor = 'not-allowed';
                            }
                        }
                    } catch(e) {}
                }, 350);

                // 5. Submit Action dengan Ekstraksi Biometrik 128-Dimensi Langsung dari Frame Snapshot
                if (btnSubmit) {
                    btnSubmit.onclick = async function() {
                        if (isBelumBolehMasuk) {
                            showAppModal('Presensi Belum Dibuka', 'Sesi lembur Anda belum memasuki jam mulai jadwal yang ditentukan. Wajib jam mulai atau setelahnya.', 'warning');
                            return;
                        }

                        if (!isFaceAligned) {
                            showAppModal('Posisikan Wajah', 'Pastikan wajah Anda berada tepat di dalam lingkaran oval hijau sebelum mengambil foto.', 'warning');
                            return;
                        }

                        btnSubmit.disabled = true;
                        btnSubmit.style.opacity = '0.7';
                        btnSubmit.innerText = '1/3 Mengambil Frame Wajah...';

                        // Tahap 1: Ambil snapshot foto resolusi tinggi
                        const snapCanvas = document.createElement('canvas');
                        snapCanvas.width = video.videoWidth || 640;
                        snapCanvas.height = video.videoHeight || 480;
                        const ctx = snapCanvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, snapCanvas.width, snapCanvas.height);

                        // Tahap 2: Ekstraksi Biometrik 128-Vektor Akurat dari Snapshot
                        btnSubmit.innerText = '2/3 Mengekstrak Biometrik AI Wajah...';

                        let liveDescriptor = null;
                        try {
                            const snapshotDetection = await faceapi.detectSingleFace(
                                snapCanvas,
                                new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.70 })
                            ).withFaceLandmarks().withFaceDescriptor();

                            if (!snapshotDetection || snapshotDetection.detection.score < 0.70) {
                                showAppModal('Wajah Tidak Jelas', 'Wajah tidak terdeteksi dengan jelas pada frame foto. Pastikan seluruh wajah Anda terlihat jelas, terang, dan menghadap lurus ke kamera.', 'warning');
                                btnSubmit.disabled = false;
                                btnSubmit.innerText = isSudahMulai ? 'Ambil Foto & Presensi Pulang Lembur' : 'Coba Lagi';
                                return;
                            }

                            liveDescriptor = Array.from(snapshotDetection.descriptor);
                        } catch(err) {
                            console.error('Biometric extraction error:', err);
                            showAppModal('Kesalahan Biometrik', 'Terjadi kesalahan saat memproses ekstraksi biometrik wajah.', 'error');
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = 'Coba Lagi';
                            return;
                        }

                        // Tahap 3: Kirim ke Backend untuk Validasi Euclidean Distance (<= 0.48) dan GPS
                        btnSubmit.innerText = '3/3 Memverifikasi dengan Data Acuan...';
                        const photoBase64 = snapCanvas.toDataURL('image/jpeg', 0.85);

                        const payload = {
                            lembur_id: lemburId,
                            foto_base64: photoBase64,
                            koordinat: (currentLat || targetLat) + ',' + (currentLng || targetLng),
                            face_descriptor: JSON.stringify(liveDescriptor),
                            _token: '{{ csrf_token() }}'
                        };

                        try {
                            const res = await fetch('{{ url('/karyawan/lembur/presensi') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(payload)
                            });

                            const data = await res.json();

                            if (res.status === 200) {
                                if (streamInstance) {
                                    streamInstance.getTracks().forEach(t => t.stop());
                                }
                                clearInterval(faceTrackingInterval);
                                showAppModal('Presensi Berhasil', data.message || 'Presensi lembur berhasil dicatat.', 'success', () => {
                                    window.location.href = lemburUrl;
                                });
                            } else {
                                const errorMsg = data.message || 'Presensi lembur tidak dapat diproses.';
                                const isWarning = errorMsg.toLowerCase().includes('belum dibuka') || errorMsg.toLowerCase().includes('jadwal') || errorMsg.toLowerCase().includes('wajib');
                                showAppModal(isWarning ? 'Pemberitahuan Waktu' : 'Pemberitahuan Biometrik', errorMsg, isWarning ? 'warning' : 'error');
                                btnSubmit.disabled = false;
                                btnSubmit.style.opacity = '1';
                                btnSubmit.innerText = isSudahMulai ? 'Ambil Foto & Presensi Pulang Lembur' : 'Coba Lagi';
                            }
                        } catch(err) {
                            showAppModal('Kesalahan Jaringan', 'Gagal menghubungi server. Periksa koneksi internet Anda.', 'error');
                            btnSubmit.disabled = false;
                            btnSubmit.style.opacity = '1';
                            btnSubmit.innerText = isSudahMulai ? 'Ambil Foto & Presensi Pulang Lembur' : 'Coba Lagi';
                        }
                    };
                }
            }

            document.addEventListener('DOMContentLoaded', initLemburCamera);
            document.addEventListener('livewire:navigated', initLemburCamera);
        })();
    </script>

    <!-- MODAL IN-APP POPUP -->
    <div id="app-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1.25rem;">
        <div id="app-modal-card" style="background: #ffffff; border-radius: 1.25rem; width: 100%; max-width: 400px; padding: 1.75rem 1.5rem; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.35); transform: scale(0.9); opacity: 0; transition: all 0.2s;">
            <div id="app-modal-icon-container" style="width: 56px; height: 56px; border-radius: 9999px; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;"></div>
            <h3 id="app-modal-title" style="font-size: 1.15rem; font-weight: 800; margin: 0 0 0.5rem 0; color: #111827;"></h3>
            <p id="app-modal-message" style="font-size: 0.875rem; line-height: 1.5; margin: 0 0 1.5rem 0; color: #4b5563;"></p>
            <button type="button" id="app-modal-btn-close" style="width: 100%; padding: 0.75rem 1.25rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 700; color: #ffffff; border: none; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>
</x-filament-panels::page>
