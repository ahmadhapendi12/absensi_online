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
            width: 200px;
            height: 270px;
            border: 2px dashed #f59e0b;
            border-radius: 50% / 50%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.35);
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 1rem;
            z-index: 5;
        }
        .face-guide-text {
            background: rgba(0, 0, 0, 0.75);
            color: #f59e0b;
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 9999px;
            letter-spacing: 0.025em;
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
        .permission-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 1rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            line-height: 1.4;
        }
        .dark .permission-alert {
            background: rgba(153, 27, 27, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }
    </style>

    <!-- BANNER PERMINTAAN IZIN KAMERA & LOKASI (SELALU MUNCUL JIKA BELUM ALLOW) -->
    <div id="persistent-permission-bar" style="display: none; margin-bottom: 1.25rem; padding: 1.25rem; border-radius: 1rem; background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(239, 68, 68, 0.08)); border: 1.5px solid rgba(245, 158, 11, 0.4); box-shadow: 0 4px 15px -3px rgba(245, 158, 11, 0.15);">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="display: flex; align-items: flex-start; gap: 0.875rem;">
                <div style="width: 44px; height: 44px; border-radius: 9999px; background: rgba(245, 158, 11, 0.2); color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <strong style="font-size: 0.9375rem; display: block; color: inherit;">Akses Kamera & Lokasi Diperlukan untuk Presensi</strong>
                    <span style="font-size: 0.8125rem; opacity: 0.85;">Aplikasi memerlukan izin Kamera (deteksi wajah) dan GPS (verifikasi radius kantor). Jika prompt browser tertutup atau tidak sengaja kepencet silang, klik tombol di sebelah kanan:</span>
                    
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

            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                <button 
                    type="button" 
                    id="btn-trigger-all-permissions" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.125rem; border-radius: 0.625rem; font-size: 0.8125rem; font-weight: 700; background: #f59e0b; color: white; border: none; cursor: pointer; box-shadow: 0 4px 8px -1px rgba(245, 158, 11, 0.35);"
                >
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    Izinkan Kamera & GPS
                </button>
                <button 
                    type="button" 
                    onclick="document.getElementById('fallback-camera-input').click()" 
                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.625rem 0.875rem; border-radius: 0.625rem; font-size: 0.8125rem; font-weight: 600; background: rgba(156, 163, 175, 0.15); color: inherit; border: 1px solid rgba(156, 163, 175, 0.3); cursor: pointer;"
                >
                    📸 Kamera HP (Bypass)
                </button>
            </div>
        </div>
    </div>

    <div class="presensi-container">

        <!-- KOLOM KIRI: KAMERA VIEWFINDER / BANNER LIBUR -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            @if($isHariLibur)
                <div class="presensi-card" style="background: rgba(16, 185, 129, 0.06); border-color: rgba(16, 185, 129, 0.3); text-align: center; padding: 2.5rem 1.5rem;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 9999px; background: rgba(16, 185, 129, 0.15); color: #10b981; margin-bottom: 1rem;">
                        <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0 0 0.5rem 0;">Hari Ini Jadwal Libur</h2>
                    <p style="font-size: 0.9375rem; color: #059669; margin: 0 0 0.5rem 0;">{{ $pesanLibur }}</p>
                    <p style="font-size: 0.8125rem; opacity: 0.75; margin: 0 0 1.5rem 0;">Anda tidak perlu melakukan presensi masuk maupun pulang hari ini.</p>
                    <a href="{{ url('/karyawan') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 700; background-color: #10b981; color: #ffffff; text-decoration: none;">
                        Kembali ke Dashboard
                    </a>
                </div>
            @else
                <div class="presensi-card" style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9375rem;">
                            <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            <span>Kamera Presensi Wajah</span>
                        </div>
                        <span id="camera-status-label" style="font-size: 0.75rem; opacity: 0.7;">Live Viewfinder</span>
                    </div>

                    <!-- Viewfinder Video & Presisi Wajah Pas di Tengah -->
                    <div class="camera-viewport">
                        <video id="webcam" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                        <canvas id="canvas" style="display: none;"></canvas>
                        <img id="fallback-preview-img" style="display: none; width: 100%; height: 100%; object-fit: cover;" src="" alt="Pratinjau Foto">

                        <!-- Oval Guide Presisi Tengah -->
                        <div id="face-oval-container" class="face-oval-guide">
                            <span class="face-guide-text">POSISIKAN WAJAH DI SINI</span>
                        </div>

                        <!-- Loading / Action State -->
                        <div id="camera-loading" style="position: absolute; inset: 0; background: rgba(0,0,0,0.88); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ffffff; font-size: 0.8125rem; gap: 0.75rem; text-align: center; padding: 1.25rem; z-index: 20;">
                            <div id="cam-loading-spinner" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                                <svg id="cam-spinner" style="width: 32px; height: 32px; color: #f59e0b; animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" style="opacity: 0.25;"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity: 0.75;"></path></svg>
                                <span id="cam-msg">Menghubungkan Kamera...</span>
                            </div>

                            <div id="cam-error-box" style="display: none; width: 100%; max-width: 300px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 9999px; background: rgba(239, 68, 68, 0.2); color: #f87171; margin-bottom: 0.5rem;">
                                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div style="font-weight: 800; font-size: 0.9375rem; color: #fca5a5; margin-bottom: 0.25rem;">Izin Kamera Diblokir Browser</div>
                                <p style="font-size: 0.75rem; color: #d1d5db; line-height: 1.4; margin: 0 0 0.875rem 0;">
                                    Browser memblokir live webcam. Anda tetap bisa mengambil foto wajah via kamera HP/perangkat:
                                </p>
                                
                                <button type="button" id="btn-cam-open-native" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: #f59e0b; color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.35);">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                    📸 Buka Kamera HP / Ambil Foto
                                </button>

                                <button type="button" id="btn-use-avatar-sample" style="width: 100%; margin-top: 0.5rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; background: rgba(255,255,255,0.12); color: #e5e7eb; border: 1px solid rgba(255,255,255,0.25); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                                    ⚡ Gunakan Foto Profil (Testing)
                                </button>

                                <div style="display: flex; justify-content: center; gap: 0.75rem; margin-top: 0.75rem;">
                                    <button type="button" id="btn-toggle-unblock-guide" style="background: none; border: none; color: #93c5fd; font-size: 0.75rem; text-decoration: underline; cursor: pointer;">
                                        ⚙️ Panduan Reset Izin Chrome
                                    </button>
                                    <button type="button" onclick="window.location.reload()" style="background: none; border: none; color: #34d399; font-size: 0.75rem; text-decoration: underline; cursor: pointer;">
                                        🔄 Muat Ulang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alternatif: Input Kamera Perangkat / HP -->
                    <input type="file" id="fallback-camera-input" accept="image/*" capture="user" style="display: none;">
                    
                    <button 
                        type="button" 
                        id="btn-fallback-camera" 
                        style="width: 100%; margin-top: 0.75rem; padding: 0.625rem; border-radius: 0.625rem; font-size: 0.8125rem; font-weight: 700; background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px dashed #f59e0b; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: background 0.2s;"
                        onmouseover="this.style.backgroundColor='rgba(245, 158, 11, 0.2)'"
                        onmouseout="this.style.backgroundColor='rgba(245, 158, 11, 0.1)'"
                    >
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Ambil Foto via Kamera Perangkat / HP (Alternatif)</span>
                    </button>

                    <!-- Kotak Peringatan Izin Kamera / GPS Ditolak (Hidden by default) -->
                    <div id="permission-blocked-box" style="display: none; margin-top: 1rem;" class="permission-alert">
                        <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                            <svg style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <strong style="display: block; font-size: 0.875rem; margin-bottom: 0.25rem;">Akses Kamera atau Lokasi Diblokir Peramban!</strong>
                                <span>Peramban (Chrome/Edge/Firefox) telah memblokir izin kamera atau GPS. Untuk mengaktifkannya:</span>
                                <ol style="margin: 0.35rem 0 0.5rem 1.25rem; padding: 0;">
                                    <li>Klik ikon <strong>Gembok / Setelan Situs</strong> di sebelah kiri URL (<code style="font-size: 0.75rem;">127.0.0.1:8000</code>).</li>
                                    <li>Ubah izin <strong>Kamera</strong> dan <strong>Lokasi</strong> menjadi <strong>Izinkan (Allow)</strong>.</li>
                                    <li>Klik tombol di bawah untuk memuat ulang halaman.</li>
                                </ol>
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                    <button type="button" onclick="window.location.reload()" style="padding: 0.375rem 0.75rem; border-radius: 0.375rem; background: #dc2626; color: white; border: none; font-weight: 700; font-size: 0.75rem; cursor: pointer;">
                                        🔄 Muat Ulang Halaman
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Presensi -->
                    <div style="margin-top: 1.25rem;">
                        @if(!$absensi)
                            <button 
                                id="btn-submit-presensi" 
                                type="button" 
                                style="width: 100%; padding: 0.875rem 1.25rem; border-radius: 0.75rem; font-size: 0.9375rem; font-weight: 700; background-color: #f59e0b; color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.35); transition: opacity 0.2s;"
                                disabled
                            >
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Ambil Foto & Absen Masuk
                            </button>
                        @elseif(!$absensi->jam_pulang)
                            <button 
                                id="btn-submit-presensi" 
                                type="button" 
                                style="width: 100%; padding: 0.875rem 1.25rem; border-radius: 0.75rem; font-size: 0.9375rem; font-weight: 700; background-color: #ef4444; color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.35); transition: opacity 0.2s;"
                                disabled
                            >
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Ambil Foto & Absen Pulang
                            </button>
                        @else
                            <div style="padding: 1rem; border-radius: 0.75rem; text-align: center; font-weight: 700; font-size: 0.875rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669;">
                                Presensi Masuk & Pulang Hari Ini Telah Selesai.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- KOLOM KANAN: STATUS LOKASI GPS, DETAIL SHIFT & FOTO MASTER ACUAN -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <!-- Foto Acuan Master Biometrik -->
            <div class="presensi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9375rem;">
                        <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Verifikasi Biometrik Wajah</span>
                    </div>
                    @if($user->profile?->foto_wajah_acuan)
                        <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                            Wajah Master Terdaftar
                        </span>
                    @else
                        <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                            Belum Ada Master
                        </span>
                    @endif
                </div>

                <div style="display: flex; align-items: center; gap: 1rem; padding-top: 0.25rem;">
                    <img 
                        src="{{ $user->avatar_url }}" 
                        alt="{{ $user->name }}" 
                        style="width: 56px; height: 56px; border-radius: 9999px; object-fit: cover; border: 2px solid #f59e0b; flex-shrink: 0;"
                        width="56"
                        height="56"
                    >
                    <div style="font-size: 0.8125rem;">
                        <strong style="display: block; font-size: 0.875rem; font-weight: 700;">{{ $user->name }}</strong>
                        <span style="opacity: 0.75;">NIK: {{ $user->profile?->nik ?? '-' }}</span>
                        @if(!$user->profile?->foto_wajah_acuan)
                            <div style="margin-top: 0.25rem;">
                                <a href="{{ url('/karyawan/profil-karyawan') }}" style="font-size: 0.75rem; color: #f59e0b; font-weight: 700; text-decoration: underline;">
                                    + Rekam Wajah Acuan di Profil
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status GPS & Radius Toleransi -->
            <div class="presensi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9375rem;">
                        <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Geolokasi & Radius Toleransi</span>
                    </div>
                    <span id="gps-badge" style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(156, 163, 175, 0.2); color: inherit;">
                        Mencari GPS...
                    </span>
                </div>

                <div id="gps-status-box" style="padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.25); background: rgba(156, 163, 175, 0.05); display: flex; align-items: flex-start; gap: 0.75rem;">
                    <svg id="gps-icon" style="width: 22px; height: 22px; color: #f59e0b; flex-shrink: 0; margin-top: 2px; animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" style="opacity: 0.25;"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity: 0.75;"></path></svg>
                    <div style="flex: 1;">
                        <div id="gps-text" style="font-size: 0.875rem; font-weight: 700;">Menghubungkan Sinyal GPS...</div>
                        <div id="gps-detail" style="font-size: 0.75rem; opacity: 0.75; margin-top: 2px;">Menghitung jarak ke titik koordinat cabang...</div>
                        <button type="button" id="btn-simulate-gps" style="display: none; margin-top: 0.5rem; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 700; background: #10b981; color: #ffffff; border: none; cursor: pointer;">
                            📍 Gunakan Lokasi Kantor (Simulasi Testing)
                        </button>
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <div class="info-item-row">
                        <span style="opacity: 0.65;">Cabang Penempatan</span>
                        <strong style="font-weight: 700;">{{ $cabang?->nama_cabang ?? 'Kantor Pusat' }}</strong>
                    </div>
                    <div class="info-item-row">
                        <span style="opacity: 0.65;">Radius Toleransi</span>
                        <strong style="font-weight: 700;">{{ $cabang?->radius_meter ?? 100 }} Meter</strong>
                    </div>
                    <div class="info-item-row">
                        <span style="opacity: 0.65;">Koordinat Cabang</span>
                        <span style="font-size: 0.75rem; font-family: monospace;">{{ $cabang?->latitude ?? -6.9175 }}, {{ $cabang?->longitude ?? 107.6191 }}</span>
                    </div>
                </div>
            </div>

            <!-- Shift & Jam Kerja -->
            <div class="presensi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9375rem;">
                        <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Jadwal Shift Anda</span>
                    </div>
                    <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #d97706;">
                        {{ $shift?->nama_shift ?? 'Reguler' }}
                    </span>
                </div>

                @if($shift)
                    <div class="info-item-row">
                        <span style="opacity: 0.65;">Jam Masuk Kerja</span>
                        <strong style="font-weight: 700; color: #059669;">{{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} WIB</strong>
                    </div>
                    <div class="info-item-row">
                        <span style="opacity: 0.65;">Jam Pulang Kerja</span>
                        <strong style="font-weight: 700; color: #4338ca;">{{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }} WIB</strong>
                    </div>
                @endif

                <div class="info-item-row">
                    <span style="opacity: 0.65;">Status Hari Ini</span>
                    @if($isHariLibur)
                        <strong style="font-weight: 700; color: #10b981;">Hari Libur (Off)</strong>
                    @elseif(!$absensi)
                        <strong style="font-weight: 700; color: #d97706;">Belum Presensi Masuk</strong>
                    @elseif(!$absensi->jam_pulang)
                        <strong style="font-weight: 700; color: #059669;">Sudah Masuk ({{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }} WIB)</strong>
                    @else
                        <strong style="font-weight: 700; color: #4338ca;">Presensi Hari Ini Selesai</strong>
                    @endif
                </div>
            </div>

            <!-- Petunjuk Presensi -->
            <div class="presensi-card" style="background: rgba(245, 158, 11, 0.05); border-color: rgba(245, 158, 11, 0.2);">
                <div style="font-size: 0.8125rem; line-height: 1.5; opacity: 0.85;">
                    <strong style="display: block; font-weight: 700; margin-bottom: 0.25rem;">Petunjuk Presensi:</strong>
                    1. Izinkan akses <strong>Kamera</strong> dan <strong>Lokasi (GPS)</strong> pada peramban Anda.<br>
                    2. Posisikan wajah pas di dalam garis oval panduan kamera.<br>
                    3. Jika kamera diblokir peramban, Anda dapat menekan tombol <strong>"Ambil Foto via Kamera Perangkat / HP"</strong> di bawah kamera.
                </div>
            </div>

        </div>

    </div>

    <script>
        (function() {
            function initPresensi() {
                const video = document.getElementById('webcam');
                const canvas = document.getElementById('canvas');
                const btnSubmit = document.getElementById('btn-submit-presensi');
                const cameraLoading = document.getElementById('camera-loading');
                const camLoadingSpinner = document.getElementById('cam-loading-spinner');
                const camErrorBox = document.getElementById('cam-error-box');
                const btnCamOpenNative = document.getElementById('btn-cam-open-native');
                const btnUseAvatarSample = document.getElementById('btn-use-avatar-sample');
                const btnToggleUnblockGuide = document.getElementById('btn-toggle-unblock-guide');
                const permissionBlockedBox = document.getElementById('permission-blocked-box');
                const fallbackInput = document.getElementById('fallback-camera-input');
                const btnFallback = document.getElementById('btn-fallback-camera');
                const previewImg = document.getElementById('fallback-preview-img');
                const faceOval = document.getElementById('face-oval-container');
                const cameraStatusLabel = document.getElementById('camera-status-label');
                
                const gpsBox = document.getElementById('gps-status-box');
                const gpsText = document.getElementById('gps-text');
                const gpsDetail = document.getElementById('gps-detail');
                const gpsBadge = document.getElementById('gps-badge');
                const gpsIcon = document.getElementById('gps-icon');
                const btnSimulateGps = document.getElementById('btn-simulate-gps');

                // Elemen Persistent Permission Bar
                const persistentPermBar = document.getElementById('persistent-permission-bar');
                const permCamBadge = document.getElementById('perm-cam-badge');
                const permGpsBadge = document.getElementById('perm-gps-badge');
                const btnTriggerPerms = document.getElementById('btn-trigger-all-permissions');

                if (!video) return;

                const targetLat = {{ $cabang?->latitude ?? -6.9175 }};
                const targetLng = {{ $cabang?->longitude ?? 107.6191 }};
                const targetRadius = {{ $cabang?->radius_meter ?? 100 }};

                let currentLat = null;
                let currentLng = null;
                let isWithinRadius = false;
                let capturedPhotoBase64 = null;
                let streamInstance = null;

                let isCameraAllowed = false;
                let isGpsAllowed = false;

                function updatePersistentPermissionBar() {
                    if (!persistentPermBar) return;

                    if (permCamBadge) {
                        if (isCameraAllowed) {
                            permCamBadge.innerHTML = '📷 Kamera: Diizinkan ✓';
                            permCamBadge.style.background = 'rgba(16, 185, 129, 0.15)';
                            permCamBadge.style.color = '#059669';
                            permCamBadge.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                        } else {
                            permCamBadge.innerHTML = '📷 Kamera: Belum Diizinkan ⚠️';
                            permCamBadge.style.background = 'rgba(239, 68, 68, 0.15)';
                            permCamBadge.style.color = '#dc2626';
                            permCamBadge.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                        }
                    }

                    if (permGpsBadge) {
                        if (isGpsAllowed) {
                            permGpsBadge.innerHTML = '📍 GPS: Diizinkan ✓';
                            permGpsBadge.style.background = 'rgba(16, 185, 129, 0.15)';
                            permGpsBadge.style.color = '#059669';
                            permGpsBadge.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                        } else {
                            permGpsBadge.innerHTML = '📍 GPS: Belum Diizinkan ⚠️';
                            permGpsBadge.style.background = 'rgba(239, 68, 68, 0.15)';
                            permGpsBadge.style.color = '#dc2626';
                            permGpsBadge.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                        }
                    }

                    // Tampilkan terus selama belum allow dua-duanya
                    if (isCameraAllowed && isGpsAllowed) {
                        persistentPermBar.style.display = 'none';
                    } else {
                        persistentPermBar.style.display = 'block';
                    }
                }

                // Initial state check
                updatePersistentPermissionBar();

                // Query permission API jika didukung browser
                if (navigator.permissions && navigator.permissions.query) {
                    navigator.permissions.query({ name: 'camera' }).then(res => {
                        if (res.state === 'granted') isCameraAllowed = true;
                        updatePersistentPermissionBar();
                        res.onchange = () => {
                            isCameraAllowed = res.state === 'granted';
                            updatePersistentPermissionBar();
                        };
                    }).catch(() => {});

                    navigator.permissions.query({ name: 'geolocation' }).then(res => {
                        if (res.state === 'granted') isGpsAllowed = true;
                        updatePersistentPermissionBar();
                        res.onchange = () => {
                            isGpsAllowed = res.state === 'granted';
                            updatePersistentPermissionBar();
                        };
                    }).catch(() => {});
                }

                function checkReadyToSubmit() {
                    if (!btnSubmit) return;
                    const hasLiveVideo = streamInstance && streamInstance.active && video && video.videoWidth > 0;
                    const hasPhoto = capturedPhotoBase64 !== null || hasLiveVideo;
                    if (isWithinRadius && hasPhoto) {
                        btnSubmit.disabled = false;
                        btnSubmit.style.opacity = '1';
                        btnSubmit.style.cursor = 'pointer';
                    } else {
                        btnSubmit.disabled = true;
                        btnSubmit.style.opacity = '0.5';
                        btnSubmit.style.cursor = 'not-allowed';
                    }
                }

                function startCamera() {
                    if (camLoadingSpinner) camLoadingSpinner.style.display = 'flex';
                    if (camErrorBox) camErrorBox.style.display = 'none';

                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                            audio: false
                        }).then(stream => {
                            streamInstance = stream;
                            isCameraAllowed = true;
                            updatePersistentPermissionBar();
                            video.srcObject = stream;
                            video.onloadedmetadata = () => {
                                if (cameraLoading) cameraLoading.style.display = 'none';
                                if (permissionBlockedBox) permissionBlockedBox.style.display = 'none';
                                if (cameraStatusLabel) cameraStatusLabel.innerText = 'Live Kamera Aktif';
                                checkReadyToSubmit();
                            };
                        }).catch(err => {
                            console.warn('Kamera WebRTC belum diizinkan atau tidak tersedia:', err.name || err);
                            isCameraAllowed = false;
                            updatePersistentPermissionBar();
                            if (camLoadingSpinner) camLoadingSpinner.style.display = 'none';
                            if (camErrorBox) camErrorBox.style.display = 'block';
                            if (cameraStatusLabel) cameraStatusLabel.innerText = 'Kamera Diblokir';
                            if (permissionBlockedBox) permissionBlockedBox.style.display = 'block';
                            checkReadyToSubmit();
                        });
                    } else {
                        isCameraAllowed = false;
                        updatePersistentPermissionBar();
                        if (camLoadingSpinner) camLoadingSpinner.style.display = 'none';
                        if (camErrorBox) camErrorBox.style.display = 'block';
                        if (cameraStatusLabel) cameraStatusLabel.innerText = 'Peramban Tidak Mendukung Live';
                        if (permissionBlockedBox) permissionBlockedBox.style.display = 'block';
                        checkReadyToSubmit();
                    }
                }

                startCamera();

                // Tombol Buka Kamera Perangkat / HP dari Viewfinder
                if (btnCamOpenNative && fallbackInput) {
                    btnCamOpenNative.onclick = function() {
                        fallbackInput.click();
                    };
                }

                // Tombol Buka Kamera Perangkat / HP dari bawah Viewfinder
                if (btnFallback && fallbackInput) {
                    btnFallback.onclick = function() {
                        fallbackInput.click();
                    };
                }

                // File input handler (Kamera native HP / File foto)
                if (fallbackInput) {
                    fallbackInput.onchange = function(e) {
                        const file = e.target.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const img = new Image();
                            img.onload = function() {
                                canvas.width = 360;
                                canvas.height = 480;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(img, 0, 0, 360, 480);
                                capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.65);

                                if (previewImg) {
                                    previewImg.src = capturedPhotoBase64;
                                    previewImg.style.display = 'block';
                                }
                                if (video) video.style.display = 'none';
                                if (cameraLoading) cameraLoading.style.display = 'none';
                                if (faceOval) faceOval.style.display = 'none';
                                if (cameraStatusLabel) cameraStatusLabel.innerText = 'Foto Berhasil Diambil';

                                checkReadyToSubmit();
                            };
                            img.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    };
                }

                // Gunakan Foto Profil / Wajah Acuan untuk Mode Testing
                if (btnUseAvatarSample) {
                    btnUseAvatarSample.onclick = function() {
                        const img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.onload = function() {
                            canvas.width = 360;
                            canvas.height = 480;
                            const ctx = canvas.getContext('2d');
                            ctx.fillStyle = '#1e293b';
                            ctx.fillRect(0, 0, 360, 480);
                            ctx.drawImage(img, 60, 100, 240, 240);
                            ctx.fillStyle = '#ffffff';
                            ctx.font = 'bold 16px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillText('{{ $user->name }}', 180, 370);
                            ctx.font = '12px sans-serif';
                            ctx.fillStyle = '#94a3b8';
                            ctx.fillText('Presensi Testing • ' + new Date().toLocaleTimeString('id-ID'), 180, 395);

                            capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.65);

                            if (previewImg) {
                                previewImg.src = capturedPhotoBase64;
                                previewImg.style.display = 'block';
                            }
                            if (video) video.style.display = 'none';
                            if (cameraLoading) cameraLoading.style.display = 'none';
                            if (faceOval) faceOval.style.display = 'none';
                            if (cameraStatusLabel) cameraStatusLabel.innerText = 'Foto Profil Terpilih (Testing)';

                            checkReadyToSubmit();
                        };
                        img.src = '{{ $user->profile?->foto_wajah_acuan ? asset("storage/" . $user->profile->foto_wajah_acuan) : $user->avatar_url }}';
                    };
                }

                // Toggle Petunjuk Buka Blokir Chrome
                if (btnToggleUnblockGuide) {
                    btnToggleUnblockGuide.onclick = function() {
                        if (permissionBlockedBox) {
                            permissionBlockedBox.style.display = permissionBlockedBox.style.display === 'none' ? 'block' : 'none';
                            permissionBlockedBox.scrollIntoView({ behavior: 'smooth' });
                        }
                    };
                }

                function calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                              Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return Math.round(R * c);
                }

                // GPS Geolocation Handler
                if (navigator.geolocation) {
                    navigator.geolocation.watchPosition(
                        position => {
                            isGpsAllowed = true;
                            updatePersistentPermissionBar();

                            currentLat = position.coords.latitude;
                            currentLng = position.coords.longitude;
                            const dist = calculateDistance(currentLat, currentLng, targetLat, targetLng);

                            if (gpsIcon) gpsIcon.style.animation = 'none';
                            if (btnSimulateGps) btnSimulateGps.style.display = 'none';

                            if (dist <= targetRadius) {
                                isWithinRadius = true;
                                if (gpsText) gpsText.innerText = 'Lokasi Sesuai (Dalam Radius)';
                                if (gpsDetail) gpsDetail.innerText = dist + ' meter dari cabang (Maks: ' + targetRadius + 'm)';
                                if (gpsBadge) {
                                    gpsBadge.innerText = 'Siap Absen';
                                    gpsBadge.style.background = 'rgba(16, 185, 129, 0.2)';
                                    gpsBadge.style.color = '#059669';
                                }
                                if (gpsBox) {
                                    gpsBox.style.background = 'rgba(16, 185, 129, 0.08)';
                                    gpsBox.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                                }
                            } else {
                                isWithinRadius = false;
                                if (gpsText) gpsText.innerText = 'Di Luar Radius Kantor!';
                                if (gpsDetail) gpsDetail.innerText = dist + ' meter dari cabang (Maks: ' + targetRadius + 'm)';
                                if (gpsBadge) {
                                    gpsBadge.innerText = 'Terlalu Jauh';
                                    gpsBadge.style.background = 'rgba(239, 68, 68, 0.2)';
                                    gpsBadge.style.color = '#dc2626';
                                }
                                if (gpsBox) {
                                    gpsBox.style.background = 'rgba(239, 68, 68, 0.08)';
                                    gpsBox.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                                }
                            }
                            checkReadyToSubmit();
                        },
                        err => {
                            console.warn('GPS belum diizinkan atau dinonaktifkan:', err.message || err);
                            isGpsAllowed = false;
                            updatePersistentPermissionBar();

                            if (gpsIcon) gpsIcon.style.animation = 'none';
                            if (gpsText) gpsText.innerText = 'Akses Lokasi (GPS) Ditolak!';
                            if (gpsDetail) gpsDetail.innerText = 'Izin lokasi diblokir oleh peramban / perangkat.';
                            if (gpsBadge) {
                                gpsBadge.innerText = 'GPS Ditolak';
                                gpsBadge.style.background = 'rgba(239, 68, 68, 0.2)';
                                gpsBadge.style.color = '#dc2626';
                            }
                            if (btnSimulateGps) {
                                btnSimulateGps.style.display = 'inline-block';
                            }
                            if (permissionBlockedBox) {
                                permissionBlockedBox.style.display = 'block';
                            }
                            checkReadyToSubmit();
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                }

                // Tombol Pemicu Manual Permintaan Izin Kamera & GPS
                if (btnTriggerPerms) {
                    btnTriggerPerms.onclick = function() {
                        startCamera();
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                pos => {
                                    isGpsAllowed = true;
                                    updatePersistentPermissionBar();
                                },
                                err => {
                                    console.warn('GPS trigger error:', err);
                                    if (permissionBlockedBox) {
                                        permissionBlockedBox.style.display = 'block';
                                        permissionBlockedBox.scrollIntoView({ behavior: 'smooth' });
                                    }
                                },
                                { enableHighAccuracy: true, timeout: 5000 }
                            );
                        }
                    };
                }

                // Tombol Simulasi Lokasi Kantor (Jika GPS Ditolak di browser/testing)
                if (btnSimulateGps) {
                    btnSimulateGps.onclick = function() {
                        currentLat = targetLat;
                        currentLng = targetLng;
                        isWithinRadius = true;
                        isGpsAllowed = true;
                        updatePersistentPermissionBar();
                        if (gpsText) gpsText.innerText = 'Lokasi Sesuai (Simulasi Kantor)';
                        if (gpsDetail) gpsDetail.innerText = '0 meter dari titik cabang (' + targetRadius + 'm toleransi)';
                        if (gpsBadge) {
                            gpsBadge.innerText = 'Siap Absen';
                            gpsBadge.style.background = 'rgba(16, 185, 129, 0.2)';
                            gpsBadge.style.color = '#059669';
                        }
                        if (gpsBox) {
                            gpsBox.style.background = 'rgba(16, 185, 129, 0.08)';
                            gpsBox.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                        }
                        checkReadyToSubmit();
                    };
                }

                // Tombol Submit Presensi
                if (btnSubmit) {
                    btnSubmit.onclick = function () {
                        if (!isWithinRadius) {
                            alert('Anda berada di luar radius kantor yang diizinkan!');
                            return;
                        }

                        let finalPhotoBase64 = capturedPhotoBase64;

                        // Jika belum ada foto statis dan live stream aktif, jepret snapshot sekarang
                        if (!finalPhotoBase64 && video && video.srcObject) {
                            canvas.width = 360;
                            canvas.height = 480;
                            const ctx = canvas.getContext('2d');
                            try {
                                ctx.drawImage(video, 0, 0, 360, 480);
                                finalPhotoBase64 = canvas.toDataURL('image/jpeg', 0.65);
                            } catch(e) {
                                console.error('Snapshot error:', e);
                            }
                        }

                        if (!finalPhotoBase64 || finalPhotoBase64.length < 100) {
                            alert('Foto presensi belum tersedia! Silakan gunakan tombol "Buka Kamera HP / Ambil Foto" untuk mengambil foto wajah.');
                            return;
                        }

                        btnSubmit.disabled = true;
                        btnSubmit.innerText = 'Memproses Presensi...';

                        const payload = {
                            foto_base64: finalPhotoBase64,
                            koordinat_masuk: (currentLat || targetLat) + ',' + (currentLng || targetLng),
                            koordinat_pulang: (currentLat || targetLat) + ',' + (currentLng || targetLng),
                            _token: '{{ csrf_token() }}'
                        };

                        fetch('{{ url('/karyawan/presensi') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json().then(data => ({ status: res.status, body: data })))
                        .then(result => {
                            if (result.status === 200) {
                                alert(result.body.message || 'Presensi berhasil disimpan!');
                                window.location.reload();
                            } else {
                                alert(result.body.message || 'Gagal menyimpan presensi.');
                                btnSubmit.disabled = false;
                                btnSubmit.innerText = 'Coba Lagi';
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Terjadi kesalahan jaringan.');
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = 'Coba Lagi';
                        });
                    };
                }
            }

            document.addEventListener('DOMContentLoaded', initPresensi);
            document.addEventListener('livewire:navigated', initPresensi);
        })();
    </script>
</x-filament-panels::page>
