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
        .dark #app-modal-card {
            background: #1f2937 !important;
            color: #f9fafb !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
        }
        .dark #app-modal-title {
            color: #f9fafb !important;
        }
        .dark #app-modal-message {
            color: #d1d5db !important;
        }
    </style>

    <!-- BANNER PERMINTAAN IZIN KAMERA & LOKASI -->
    <div id="persistent-permission-bar" style="display: none; margin-bottom: 1.25rem; padding: 1.25rem; border-radius: 1rem; background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(239, 68, 68, 0.08)); border: 1.5px solid rgba(245, 158, 11, 0.4); box-shadow: 0 4px 15px -3px rgba(245, 158, 11, 0.15);">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="display: flex; align-items: flex-start; gap: 0.875rem;">
                <div style="width: 44px; height: 44px; border-radius: 9999px; background: rgba(245, 158, 11, 0.2); color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <strong style="font-size: 0.9375rem; display: block; color: inherit;">Akses Kamera & Lokasi Diperlukan</strong>
                    <span style="font-size: 0.8125rem; opacity: 0.85;">Aplikasi memerlukan izin kamera untuk verifikasi wajah dan GPS untuk verifikasi posisi kantor.</span>
                    
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
            </div>
        </div>
    </div>

    <!-- BANNER PERINGATAN: BELUM ADA BIOMETRIK WAJAH -->
    @if(!$user->profile?->foto_wajah_acuan)
        <div style="margin-bottom: 1.25rem; padding: 1.25rem; border-radius: 1rem; background: rgba(239, 68, 68, 0.08); border: 1.5px dashed #ef4444; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.875rem;">
                <div style="width: 44px; height: 44px; border-radius: 9999px; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <strong style="display: block; font-size: 0.9375rem; color: #dc2626;">Biometrik Wajah Belum Terdaftar</strong>
                    <span style="font-size: 0.8125rem; opacity: 0.85; line-height: 1.35; display: block; margin-top: 2px;">
                        Silakan daftarkan foto wajah acuan pada menu Profil Karyawan sebelum melakukan presensi.
                    </span>
                </div>
            </div>
            <a href="{{ url('/karyawan/profil-karyawan') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.125rem; border-radius: 0.5rem; background: #dc2626; color: white; font-weight: 700; font-size: 0.8125rem; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.35);">
                <span>Daftarkan Wajah Sekarang →</span>
            </a>
        </div>
    @endif

    <div class="presensi-container">

        <!-- KOLOM KIRI: KAMERA VIEWFINDER LIVE & AI BIOMETRIK -->
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
                            <span>Kamera Presensi</span>
                        </div>
                        <span id="camera-status-label" style="font-size: 0.75rem; opacity: 0.8; font-weight: 600;">Menghubungkan Kamera...</span>
                    </div>

                    <!-- Viewfinder Video Kamera Langsung & AI Landmark Oval -->
                    <div class="camera-viewport">
                        <video id="webcam" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                        <canvas id="canvas" style="display: none;"></canvas>

                        <!-- Oval Guide AI Deteksi & Kesejajaran Wajah -->
                        <div id="face-oval-container" class="face-oval-guide">
                            <span id="face-guide-text" class="face-guide-text">MEMUAT MODEL BIOMETRIK...</span>
                        </div>

                        <!-- Loading State Kamera -->
                        <div id="camera-loading" style="position: absolute; inset: 0; background: rgba(0,0,0,0.88); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ffffff; font-size: 0.8125rem; gap: 0.75rem; text-align: center; padding: 1.25rem; z-index: 20;">
                            <div id="cam-loading-spinner" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                                <svg id="cam-spinner" style="width: 32px; height: 32px; color: #f59e0b; animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" style="opacity: 0.25;"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity: 0.75;"></path></svg>
                                <span id="cam-msg">Menghubungkan Kamera & Memuat Model AI...</span>
                            </div>

                            <div id="cam-error-box" style="display: none; width: 100%; max-width: 300px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 9999px; background: rgba(239, 68, 68, 0.2); color: #f87171; margin-bottom: 0.5rem;">
                                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div style="font-weight: 800; font-size: 0.9375rem; color: #fca5a5; margin-bottom: 0.25rem;">Akses Kamera Belum Diizinkan</div>
                                <p style="font-size: 0.75rem; color: #d1d5db; line-height: 1.4; margin: 0 0 0.875rem 0;">
                                    Sistem memerlukan akses kamera langsung untuk verifikasi presensi. Silakan izinkan akses kamera pada pengaturan peramban Anda.
                                </p>
                                
                                <div style="display: flex; justify-content: center; gap: 0.75rem; margin-top: 0.5rem;">
                                    <button type="button" id="btn-toggle-unblock-guide" style="background: none; border: none; color: #93c5fd; font-size: 0.75rem; text-decoration: underline; cursor: pointer;">
                                        ⚙️ Panduan Pengaturan Izin
                                    </button>
                                    <button type="button" onclick="window.location.reload()" style="background: none; border: none; color: #34d399; font-size: 0.75rem; text-decoration: underline; cursor: pointer;">
                                        🔄 Muat Ulang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kotak Peringatan Izin Kamera / GPS Ditolak -->
                    <div id="permission-blocked-box" style="display: none; margin-top: 1rem;" class="permission-alert">
                        <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                            <svg style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <strong style="display: block; font-size: 0.875rem; margin-bottom: 0.25rem;">Izin Kamera atau Lokasi Belum Aktif</strong>
                                <span>Peramban belum mengizinkan kamera atau GPS. Untuk mengaktifkannya:</span>
                                <ol style="margin: 0.35rem 0 0.5rem 1.25rem; padding: 0;">
                                    <li>Klik ikon <strong>Setelan Situs / Izin</strong> pada bilah alamat peramban (<code style="font-size: 0.75rem;">{{ request()->getHost() }}</code>).</li>
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
                                <span>Presensi Masuk</span>
                            </button>
                        @elseif(!$absensi->jam_pulang)
                            <button 
                                id="btn-submit-presensi" 
                                type="button" 
                                style="width: 100%; padding: 0.875rem 1.25rem; border-radius: 0.75rem; font-size: 0.9375rem; font-weight: 700; background-color: #ef4444; color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.35); transition: opacity 0.2s;"
                                disabled
                            >
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span>Presensi Pulang</span>
                            </button>
                        @else
                            <div style="padding: 1rem; border-radius: 0.75rem; text-align: center; font-weight: 700; font-size: 0.875rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669;">
                                ✓ Presensi Masuk & Pulang Hari Ini Telah Selesai.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- KOLOM KANAN: STATUS BIOMETRIK MASTER, GPS & JADWAL SHIFT -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <!-- Foto Acuan Master Biometrik -->
            <div class="presensi-card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9375rem;">
                        <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Data Biometrik Wajah</span>
                    </div>
                    @if($user->profile?->foto_wajah_acuan)
                        <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                            ✓ Terdaftar
                        </span>
                    @else
                        <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                            Belum Terdaftar
                        </span>
                    @endif
                </div>

                <div style="display: flex; align-items: center; gap: 1rem; padding-top: 0.25rem;">
                    <img 
                        src="{{ $user->profile?->foto_wajah_acuan ? asset('storage/' . $user->profile->foto_wajah_acuan) : $user->avatar_url }}" 
                        alt="{{ $user->name }}" 
                        style="width: 56px; height: 56px; border-radius: 9999px; object-fit: cover; border: 2.5px solid {{ $user->profile?->foto_wajah_acuan ? '#10b981' : '#f59e0b' }}; flex-shrink: 0;"
                        width="56"
                        height="56"
                    >
                    <div style="font-size: 0.8125rem;">
                        <strong style="display: block; font-size: 0.875rem; font-weight: 700;">{{ $user->name }}</strong>
                        <span style="opacity: 0.75;">NIK: {{ $user->profile?->nik ?? '-' }}</span>
                        @if($user->profile?->foto_wajah_acuan)
                            <div style="font-size: 0.75rem; color: #059669; margin-top: 0.25rem; font-weight: 600;">
                                ✓ Terverifikasi
                            </div>
                        @else
                            <div style="margin-top: 0.25rem;">
                                <a href="{{ url('/karyawan/profil-karyawan') }}" style="font-size: 0.75rem; color: #dc2626; font-weight: 700; text-decoration: underline;">
                                    + Daftarkan Wajah di Profil
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
                        <span>Geolokasi & Radius Kantor</span>
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
                        <button type="button" id="btn-simulate-gps" style="display: none; margin-top: 0.625rem; padding: 0.5rem 0.875rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700; background: #059669; color: #ffffff; border: none; cursor: pointer; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);">
                            📍 Gunakan Titik Kantor (Mode Pengujian Laptop)
                        </button>
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <div class="info-item-row">
                        <span style="opacity: 0.65;">Cabang Penempatan</span>
                        <strong style="font-weight: 700;">{{ $cabang?->nama_cabang ?? 'Kantor Pusat' }}</strong>
                    </div>
                    <div class="info-item-row">
                        <span style="opacity: 0.65;">Radius Maksimal</span>
                        <strong style="font-weight: 700;">{{ $cabang?->radius_meter ?? 50 }} Meter</strong>
                    </div>
                </div>
            </div>

            <!-- Detail Jadwal & Shift Kerja -->
            <div class="presensi-card">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9375rem; margin-bottom: 0.75rem;">
                    <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Informasi Shift Kerja</span>
                </div>

                @if($shift)
                    <div>
                        <div class="info-item-row">
                            <span style="opacity: 0.65;">Nama Shift</span>
                            <strong style="font-weight: 700; color: #f59e0b;">{{ $shift->nama_shift }}</strong>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.65;">Jam Masuk</span>
                            <strong style="font-weight: 700;">{{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} WIB</strong>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.65;">Jam Pulang</span>
                            <strong style="font-weight: 700;">{{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }} WIB</strong>
                        </div>
                        <div class="info-item-row">
                            <span style="opacity: 0.65;">Toleransi Keterlambatan</span>
                            <strong style="font-weight: 700;">{{ $shift->toleransi_terlambat_menit ?? 15 }} Menit</strong>
                        </div>
                    </div>
                @else
                    <p style="font-size: 0.8125rem; opacity: 0.75; margin: 0;">Anda belum memiliki jadwal shift yang ditentukan. Hubungi Super Admin.</p>
                @endif
            </div>

            @if(isset($lemburHariIni) && $lemburHariIni)
                <div class="presensi-card" style="border-left: 4px solid #f59e0b; background: rgba(245, 158, 11, 0.06); padding: 1.25rem;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                        <div>
                            <span style="display: inline-block; font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #d97706; margin-bottom: 0.25rem;">Penugasan Lembur Hari Ini</span>
                            <h4 style="font-size: 0.9375rem; font-weight: 700; margin: 0 0 0.25rem 0;">{{ $lemburHariIni->alasan_lembur }}</h4>
                            <p style="font-size: 0.8125rem; opacity: 0.8; margin: 0;">
                                @if(!$lemburHariIni->foto_mulai)
                                    Status: Siap Mulai. Silakan ambil presensi masuk lembur.
                                @elseif(!$lemburHariIni->foto_selesai)
                                    Status: Sedang Lembur sejak {{ $lemburHariIni->jam_mulai_aktual }} WIB.
                                @else
                                    Status: Selesai ({{ round($lemburHariIni->durasi_menit / 60, 1) }} Jam).
                                @endif
                            </p>
                        </div>
                        @if(!$lemburHariIni->foto_mulai || !$lemburHariIni->foto_selesai)
                            <a href="{{ url('/karyawan/lembur-presensi?id=' . $lemburHariIni->id) }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 0.875rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: #f59e0b; color: white; text-decoration: none; flex-shrink: 0; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);">
                                📷 Kamera Lembur →
                            </a>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- Script Face-API & Live Biometrik Real-time -->
    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        (function() {
            function initPresensi() {
                const video = document.getElementById('webcam');
                const canvas = document.getElementById('canvas');
                const faceOval = document.getElementById('face-oval-container');
                const faceGuideText = document.getElementById('face-guide-text');
                const cameraLoading = document.getElementById('camera-loading');
                const camLoadingSpinner = document.getElementById('cam-loading-spinner');
                const camErrorBox = document.getElementById('cam-error-box');
                const cameraStatusLabel = document.getElementById('camera-status-label');
                const btnSubmit = document.getElementById('btn-submit-presensi');
                const btnToggleUnblockGuide = document.getElementById('btn-toggle-unblock-guide');
                const permissionBlockedBox = document.getElementById('permission-blocked-box');

                const persistentPermBar = document.getElementById('persistent-permission-bar');
                const permCamBadge = document.getElementById('perm-cam-badge');
                const permGpsBadge = document.getElementById('perm-gps-badge');
                const btnTriggerPerms = document.getElementById('btn-trigger-all-permissions');

                const gpsText = document.getElementById('gps-text');
                const gpsDetail = document.getElementById('gps-detail');
                const gpsBadge = document.getElementById('gps-badge');
                const gpsBox = document.getElementById('gps-status-box');
                const gpsIcon = document.getElementById('gps-icon');
                const btnSimulateGps = document.getElementById('btn-simulate-gps');

                const targetLat = {{ $cabang?->latitude ?? -6.2088 }};
                const targetLng = {{ $cabang?->longitude ?? 106.8456 }};
                const targetRadius = {{ $cabang?->radius_meter ?? 50 }};
                const hasMasterFace = {{ $user->profile?->foto_wajah_acuan ? 'true' : 'false' }};

                let streamInstance = null;
                let currentLat = null;
                let currentLng = null;
                let isWithinRadius = false;
                let isCameraAllowed = false;
                let isGpsAllowed = false;
                let isSimulatedGps = false;

                // Face AI Variables
                const MODEL_URL = '{{ asset('models') }}';
                let isModelsLoaded = false;
                let lastDetectedDescriptor = null;
                let isFaceAligned = false;
                let faceTrackingInterval = null;

                // 1. Muat Model Face-API Ringan (TinyFaceDetector, 68 Landmarks, FaceRecognition)
                async function loadFaceApiModels() {
                    try {
                        if (window.faceapi && !isModelsLoaded) {
                            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                            isModelsLoaded = true;
                            if (faceGuideText) faceGuideText.innerText = 'POSISIKAN WAJAH DI SINI';
                        }
                    } catch(e) {
                        console.warn('Gagal memuat model face-api:', e);
                        if (faceGuideText) faceGuideText.innerText = 'PANDANG LURUS KE KAMERA';
                    }
                }

                loadFaceApiModels();

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

                    if (isCameraAllowed && isGpsAllowed) {
                        persistentPermBar.style.display = 'none';
                    } else {
                        persistentPermBar.style.display = 'block';
                    }
                }

                updatePersistentPermissionBar();

                // 2. Cek Status Izin Browser
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
                    
                    if (isWithinRadius && hasLiveVideo && isFaceAligned && hasMasterFace) {
                        btnSubmit.disabled = false;
                        btnSubmit.style.opacity = '1';
                        btnSubmit.style.cursor = 'pointer';
                    } else {
                        btnSubmit.disabled = true;
                        btnSubmit.style.opacity = '0.5';
                        btnSubmit.style.cursor = 'not-allowed';
                    }
                }

                // 3. Real-time Face Detection Loop (Pemeriksaan Posisi, Jarak & Kesejajaran Wajah)
                async function runFaceDetectionLoop() {
                    if (!isModelsLoaded || !video || video.paused || video.ended || !streamInstance || !streamInstance.active) {
                        return;
                    }

                    try {
                        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.70 });
                        const detection = await faceapi.detectSingleFace(video, options).withFaceLandmarks().withFaceDescriptor();

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

                            // 1. Validasi Posisi: Wajah harus berada di tengah area panduan oval
                            const isCentered = (diffX <= videoW * 0.18) && (diffY <= videoH * 0.20);

                            // 2. Validasi Ukuran/Jarak: Wajah harus proporsional (mengisi oval 24% - 65% frame, bukan 25% terpotong)
                            const isProperSize = (box.width >= videoW * 0.24) && (box.width <= videoW * 0.65) && (box.height >= videoH * 0.28);

                            // 3. Validasi Kesimetrisan Hidung & Rahang (menghadap lurus ke depan, bukan menyamping)
                            const noseTip = nose[3] || nose[0];
                            const leftJaw = jaw[2] || jaw[0];
                            const rightJaw = jaw[14] || jaw[16];
                            const distLeft = Math.hypot(noseTip.x - leftJaw.x, noseTip.y - leftJaw.y);
                            const distRight = Math.hypot(rightJaw.x - noseTip.x, rightJaw.y - noseTip.y);
                            const symmetryRatio = distLeft / (distRight || 1);
                            const isFacingFront = (symmetryRatio >= 0.70) && (symmetryRatio <= 1.45);

                            // 4. Validasi Kemiringan Kepala (upright, tidak miring lebih dari 15 derajat)
                            const eyeDeltaY = Math.abs(leftEye[0].y - rightEye[3].y);
                            const eyeDeltaX = Math.abs(leftEye[0].x - rightEye[3].x);
                            const tiltRatio = eyeDeltaY / (eyeDeltaX || 1);
                            const isUpright = (tiltRatio <= 0.22);

                            if (!isProperSize && box.width < videoW * 0.24) {
                                isFaceAligned = false;
                                if (faceOval) faceOval.className = 'face-oval-guide tilted';
                                if (faceGuideText) {
                                    faceGuideText.innerText = 'DEKATKAN WAJAH KE KAMERA';
                                    faceGuideText.style.color = '#fbbf24';
                                }
                                if (cameraStatusLabel) {
                                    cameraStatusLabel.innerText = 'Dekatkan Wajah';
                                    cameraStatusLabel.style.color = '#f59e0b';
                                }
                            } else if (!isProperSize && box.width > videoW * 0.65) {
                                isFaceAligned = false;
                                if (faceOval) faceOval.className = 'face-oval-guide tilted';
                                if (faceGuideText) {
                                    faceGuideText.innerText = 'MUNDUR SEDIKIT DARI KAMERA';
                                    faceGuideText.style.color = '#fbbf24';
                                }
                                if (cameraStatusLabel) {
                                    cameraStatusLabel.innerText = 'Mundur Sedikit';
                                    cameraStatusLabel.style.color = '#f59e0b';
                                }
                            } else if (!isCentered) {
                                isFaceAligned = false;
                                if (faceOval) faceOval.className = 'face-oval-guide tilted';
                                if (faceGuideText) {
                                    faceGuideText.innerText = 'POSISIKAN WAJAH DI TENGAH OVAL';
                                    faceGuideText.style.color = '#fbbf24';
                                }
                                if (cameraStatusLabel) {
                                    cameraStatusLabel.innerText = 'Posisikan di Tengah Oval';
                                    cameraStatusLabel.style.color = '#f59e0b';
                                }
                            } else if (!isFacingFront) {
                                isFaceAligned = false;
                                if (faceOval) faceOval.className = 'face-oval-guide tilted';
                                if (faceGuideText) {
                                    faceGuideText.innerText = 'HADAP LURUS KE DEPAN';
                                    faceGuideText.style.color = '#fbbf24';
                                }
                                if (cameraStatusLabel) {
                                    cameraStatusLabel.innerText = 'Hadap Lurus ke Depan';
                                    cameraStatusLabel.style.color = '#f59e0b';
                                }
                            } else if (!isUpright) {
                                isFaceAligned = false;
                                if (faceOval) faceOval.className = 'face-oval-guide tilted';
                                if (faceGuideText) {
                                    faceGuideText.innerText = 'POSISIKAN KEPALA TEGAK';
                                    faceGuideText.style.color = '#fbbf24';
                                }
                                if (cameraStatusLabel) {
                                    cameraStatusLabel.innerText = 'Posisikan Kepala Tegak';
                                    cameraStatusLabel.style.color = '#f59e0b';
                                }
                            } else {
                                // SELURUH KRITERIA TERPENUHI: Wajah benar-benar pas di dalam oval
                                isFaceAligned = true;
                                lastDetectedDescriptor = Array.from(detection.descriptor);

                                if (faceOval) {
                                    faceOval.className = 'face-oval-guide aligned';
                                }
                                if (faceGuideText) {
                                    faceGuideText.innerText = '✓ POSISI WAJAH PAS';
                                    faceGuideText.style.color = '#34d399';
                                }
                                if (cameraStatusLabel) {
                                    cameraStatusLabel.innerText = 'Posisi Wajah Sesuai';
                                    cameraStatusLabel.style.color = '#10b981';
                                }
                            }
                        } else {
                            isFaceAligned = false;
                            if (faceOval) {
                                faceOval.className = 'face-oval-guide unaligned';
                            }
                            if (faceGuideText) {
                                faceGuideText.innerText = 'ARAHKAN WAJAH KE KAMERA';
                                faceGuideText.style.color = '#f87171';
                            }
                            if (cameraStatusLabel) {
                                cameraStatusLabel.innerText = 'Mendeteksi Wajah...';
                                cameraStatusLabel.style.color = 'inherit';
                            }
                        }
                    } catch(err) {
                        console.warn('Face detection loop exception:', err);
                    }

                    checkReadyToSubmit();
                }

                // 4. Nyalakan Kamera Live WebRTC
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

                                if (!faceTrackingInterval) {
                                    faceTrackingInterval = setInterval(runFaceDetectionLoop, 350);
                                }
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

                // 5. Toggle Petunjuk Buka Blokir
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

                // 6. GPS Geolocation Handler
                if (navigator.geolocation) {
                    navigator.geolocation.watchPosition(
                        position => {
                            if (isSimulatedGps) return;
                            isGpsAllowed = true;
                            updatePersistentPermissionBar();

                            currentLat = position.coords.latitude;
                            currentLng = position.coords.longitude;
                            const dist = calculateDistance(currentLat, currentLng, targetLat, targetLng);
                            const accuracy = Math.round(position.coords.accuracy || 0);

                            if (gpsIcon) gpsIcon.style.animation = 'none';

                            // Toleransi akurasi perangkat GPS mobile (hingga 50 meter)
                            const effectiveRadius = targetRadius + Math.min(accuracy, 50);
                            const isLocalDev = window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost';

                            // Tampilkan tombol simulasi di lingkungan dev agar memudahkan pengujian
                            if (btnSimulateGps) {
                                btnSimulateGps.style.display = (isLocalDev || dist > effectiveRadius) ? 'inline-block' : 'none';
                            }

                            if (dist <= effectiveRadius) {
                                isWithinRadius = true;
                                if (gpsText) gpsText.innerText = 'Lokasi Sesuai Radius Kantor';
                                if (gpsDetail) gpsDetail.innerText = dist + ' meter dari cabang (Radius: ' + targetRadius + 'm, Akurasi: ±' + accuracy + 'm)';
                                if (gpsBadge) {
                                    gpsBadge.innerText = 'Lokasi Sesuai';
                                    gpsBadge.style.background = 'rgba(16, 185, 129, 0.2)';
                                    gpsBadge.style.color = '#059669';
                                }
                                if (gpsBox) {
                                    gpsBox.style.background = 'rgba(16, 185, 129, 0.08)';
                                    gpsBox.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                                }
                            } else {
                                isWithinRadius = false;
                                if (gpsText) gpsText.innerText = 'Di Luar Radius Kantor';
                                if (accuracy >= 250 || isLocalDev) {
                                    if (gpsDetail) gpsDetail.innerHTML = dist + ' meter dari cabang (Maksimal: ' + targetRadius + 'm, Akurasi Laptop: ±' + accuracy + 'm).<br><span style="color: #f59e0b; font-weight: 600;">Laptop tidak memiliki chip satelit GPS (posisi dari IP/Wi-Fi ISP). Klik tombol hijau di bawah untuk pengujian:</span>';
                                } else {
                                    if (gpsDetail) gpsDetail.innerText = dist + ' meter dari cabang (Maksimal: ' + targetRadius + 'm, Akurasi: ±' + accuracy + 'm)';
                                }
                                if (gpsBadge) {
                                    gpsBadge.innerText = 'Di Luar Radius';
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
                        error => {
                            console.warn('GPS Error:', error);
                            isGpsAllowed = false;
                            updatePersistentPermissionBar();
                            if (gpsText) gpsText.innerText = 'Sinyal GPS Tidak Terdeteksi';
                            if (gpsDetail) gpsDetail.innerText = 'Aktifkan izin lokasi dan GPS pada perangkat Anda.';
                            if (btnSimulateGps) btnSimulateGps.style.display = 'inline-block';
                            checkReadyToSubmit();
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                }

                // Tombol Pemicu Manual Permintaan Izin
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

                // Tombol Simulasi Lokasi Kantor (Pengujian di Laptop / Localhost)
                if (btnSimulateGps) {
                    btnSimulateGps.onclick = function() {
                        isSimulatedGps = true;
                        currentLat = targetLat;
                        currentLng = targetLng;
                        isWithinRadius = true;
                        isGpsAllowed = true;
                        updatePersistentPermissionBar();
                        if (gpsText) gpsText.innerText = 'Lokasi Sesuai (Simulasi Kantor)';
                        if (gpsDetail) gpsDetail.innerText = 'Titik lokasi kantor aktif (' + targetRadius + 'm radius)';
                        if (gpsBadge) {
                            gpsBadge.innerText = 'Lokasi Sesuai';
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

                // Fungsi Notifikasi Web In-App (Pengganti alert bawaan browser)
                function showAppModal(title, message, type = 'error', callback = null) {
                    const overlay = document.getElementById('app-modal-overlay');
                    const card = document.getElementById('app-modal-card');
                    const iconContainer = document.getElementById('app-modal-icon-container');
                    const titleEl = document.getElementById('app-modal-title');
                    const msgEl = document.getElementById('app-modal-message');
                    const btnClose = document.getElementById('app-modal-btn-close');

                    if (!overlay || !card) {
                        window.alert(title + "\n" + message);
                        if (typeof callback === 'function') callback();
                        return;
                    }

                    titleEl.innerText = title;
                    msgEl.innerText = message;

                    if (type === 'success') {
                        iconContainer.style.background = 'rgba(16, 185, 129, 0.15)';
                        iconContainer.style.color = '#10b981';
                        iconContainer.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
                        btnClose.style.background = '#10b981';
                        btnClose.innerText = 'Selesai';
                    } else if (type === 'warning') {
                        iconContainer.style.background = 'rgba(245, 158, 11, 0.15)';
                        iconContainer.style.color = '#f59e0b';
                        iconContainer.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                        btnClose.style.background = '#f59e0b';
                        btnClose.innerText = 'Mengerti';
                    } else {
                        iconContainer.style.background = 'rgba(239, 68, 68, 0.15)';
                        iconContainer.style.color = '#ef4444';
                        iconContainer.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';
                        btnClose.style.background = '#ef4444';
                        btnClose.innerText = 'Tutup';
                    }

                    overlay.style.display = 'flex';
                    requestAnimationFrame(() => {
                        card.style.transform = 'scale(1)';
                        card.style.opacity = '1';
                    });

                    const closeHandler = () => {
                        card.style.transform = 'scale(0.9)';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            overlay.style.display = 'none';
                            if (typeof callback === 'function') {
                                callback();
                            }
                        }, 180);
                        btnClose.removeEventListener('click', closeHandler);
                    };

                    btnClose.addEventListener('click', closeHandler);
                }

                // 7. Tombol Submit Presensi: Ekstraksi Biometrik Instan dari Frame & Eksekusi Verifikasi
                if (btnSubmit) {
                    btnSubmit.onclick = async function () {
                        if (!hasMasterFace) {
                            showAppModal('Biometrik Belum Terdaftar', 'Data biometrik wajah belum terdaftar. Silakan lakukan perekaman wajah pada menu Profil Karyawan terlebih dahulu.', 'warning');
                            return;
                        }

                        if (!isWithinRadius) {
                            showAppModal('Di Luar Radius Kantor', 'Posisi Anda berada di luar radius kantor yang ditentukan.', 'warning');
                            return;
                        }

                        if (!streamInstance || !video || video.paused || video.ended || video.videoWidth === 0) {
                            showAppModal('Kamera Belum Aktif', 'Pastikan kamera aktif dan mengarah ke wajah Anda.', 'warning');
                            return;
                        }

                        // Tahap 1: Pengambilan Frame Foto Wajah Asli dari Kamera
                        btnSubmit.disabled = true;
                        btnSubmit.innerText = '1/3 Mengambil Frame Wajah...';

                        canvas.width = video.videoWidth || 640;
                        canvas.height = video.videoHeight || 480;
                        const ctx = canvas.getContext('2d');
                        try {
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        } catch(e) {
                            console.error('Snapshot error:', e);
                            showAppModal('Gagal Mengambil Foto', 'Gagal mengambil frame foto dari kamera perangkat.', 'error');
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = 'Coba Lagi';
                            checkReadyToSubmit();
                            return;
                        }

                        // Tahap 2: Ekstraksi Biometrik Wajah dari Frame Snapshot (Memastikan wajah terdeteksi utuh)
                        btnSubmit.innerText = '2/3 Mengekstrak Biometrik Wajah...';

                        let liveDescriptor = null;
                        try {
                            const snapshotDetection = await faceapi.detectSingleFace(
                                canvas, 
                                new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.70 })
                            ).withFaceLandmarks().withFaceDescriptor();

                            if (!snapshotDetection || snapshotDetection.detection.score < 0.70) {
                                showAppModal('Wajah Tidak Jelas', 'Wajah tidak terdeteksi dengan jelas pada frame foto. Pastikan seluruh wajah Anda terlihat jelas, terang, dan menghadap lurus ke kamera.', 'warning');
                                btnSubmit.disabled = false;
                                btnSubmit.innerText = 'Coba Lagi';
                                checkReadyToSubmit();
                                return;
                            }

                            liveDescriptor = Array.from(snapshotDetection.descriptor);
                        } catch(err) {
                            console.error('Biometric extraction error:', err);
                            showAppModal('Kesalahan Biometrik', 'Terjadi kesalahan saat memproses ekstraksi biometrik wajah.', 'error');
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = 'Coba Lagi';
                            checkReadyToSubmit();
                            return;
                        }

                        // Tahap 3: Verifikasi Service Backend dengan Data Wajah Acuan Master
                        btnSubmit.innerText = '3/3 Memverifikasi dengan Data Acuan...';

                        const finalPhotoBase64 = canvas.toDataURL('image/jpeg', 0.85);
                        const payload = {
                            foto_base64: finalPhotoBase64,
                            koordinat_masuk: (currentLat || targetLat) + ',' + (currentLng || targetLng),
                            koordinat_pulang: (currentLat || targetLat) + ',' + (currentLng || targetLng),
                            face_descriptor: JSON.stringify(liveDescriptor),
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
                                if (streamInstance) {
                                    streamInstance.getTracks().forEach(track => track.stop());
                                }
                                if (faceTrackingInterval) clearInterval(faceTrackingInterval);
                                showAppModal('Presensi Berhasil', result.body.message || 'Presensi berhasil dicatat.', 'success', () => {
                                    window.location.reload();
                                });
                            } else {
                                const errorMsg = result.body.message || 
                                    (result.body.errors?.biometrik?.[0]) || 
                                    (result.body.errors?.lokasi?.[0]) || 
                                    (result.body.errors?.shift?.[0]) || 
                                    'Presensi tidak dapat diproses.';
                                const isBiometricError = errorMsg.toLowerCase().includes('biometrik') || errorMsg.toLowerCase().includes('wajah');
                                showAppModal('Pemberitahuan Presensi', errorMsg, isBiometricError ? 'error' : 'warning');
                                btnSubmit.disabled = false;
                                btnSubmit.innerText = 'Coba Lagi';
                                checkReadyToSubmit();
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showAppModal('Kesalahan Jaringan', 'Terjadi kesalahan koneksi jaringan. Silakan periksa koneksi internet Anda.', 'error');
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = 'Coba Lagi';
                            checkReadyToSubmit();
                        });
                    };
                }
            }

            document.addEventListener('DOMContentLoaded', initPresensi);
            document.addEventListener('livewire:navigated', initPresensi);
        })();
    </script>

    <!-- MODAL NOTIFIKASI WEB IN-APP (PENGGANTI ALERT BROWSER) -->
    <div id="app-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1.25rem;">
        <div id="app-modal-card" style="background: #ffffff; border-radius: 1.25rem; width: 100%; max-width: 400px; padding: 1.75rem 1.5rem; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.35); transform: scale(0.9); opacity: 0; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
            <div id="app-modal-icon-container" style="width: 56px; height: 56px; border-radius: 9999px; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;">
            </div>
            <h3 id="app-modal-title" style="font-size: 1.15rem; font-weight: 800; margin: 0 0 0.5rem 0; color: #111827;"></h3>
            <p id="app-modal-message" style="font-size: 0.875rem; line-height: 1.5; margin: 0 0 1.5rem 0; color: #4b5563;"></p>
            <button type="button" id="app-modal-btn-close" style="width: 100%; padding: 0.75rem 1.25rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 700; color: #ffffff; border: none; cursor: pointer; transition: opacity 0.15s;">
                Tutup
            </button>
        </div>
    </div>
</x-filament-panels::page>
