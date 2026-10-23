<x-filament-panels::page>
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .profile-container {
                grid-template-columns: 360px 1fr;
                gap: 1.75rem;
                align-items: start;
            }
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(156, 163, 175, 0.25);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .profile-card {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(75, 85, 99, 0.4);
        }
        .form-input-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 0.625rem;
            border: 1px solid rgba(156, 163, 175, 0.35);
            background: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            color: inherit;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-input-control:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }
        .dark .form-input-control {
            background: rgba(31, 41, 55, 0.8);
            border-color: rgba(75, 85, 99, 0.6);
            color: #f9fafb;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.625rem 0;
            border-bottom: 1px solid rgba(156, 163, 175, 0.15);
            font-size: 0.8125rem;
        }
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .enroll-viewport {
            position: relative;
            background: #000;
            border-radius: 0.75rem;
            overflow: hidden;
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            aspect-ratio: 3/4;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .enroll-oval {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 170px;
            height: 220px;
            border: 2px dashed #f59e0b;
            border-radius: 50% / 50%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.35);
            pointer-events: none;
        }
    </style>

    <div class="profile-container">

        <!-- SISI KIRI: IDENTITAS KARYAWAN -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <div class="profile-card">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; padding-bottom: 1.25rem; border-bottom: 1px solid rgba(156, 163, 175, 0.2);">
                    <div style="position: relative; margin-bottom: 1rem;">
                        <img 
                            src="{{ $user->avatar_url }}" 
                            alt="{{ $user->name }}" 
                            style="width: 88px; height: 88px; border-radius: 9999px; object-fit: cover; border: 3px solid #f59e0b; box-shadow: 0 4px 10px -2px rgba(245, 158, 11, 0.25);"
                            width="88"
                            height="88"
                        >
                        <span style="position: absolute; bottom: 4px; right: 4px; width: 14px; height: 14px; background-color: #10b981; border: 2px solid white; border-radius: 9999px;" title="Status Aktif"></span>
                    </div>

                    <h2 style="font-size: 1.25rem; font-weight: 800; line-height: 1.2; margin: 0 0 0.35rem 0;">{{ $user->name }}</h2>
                    
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.375rem; margin-top: 0.25rem;">
                        <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                            {{ $cabang?->nama_cabang ?? 'Kantor Pusat' }}
                        </span>
                        <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                            Karyawan Aktif
                        </span>
                    </div>
                </div>

                <div style="padding-top: 1rem;">
                    <div class="info-row">
                        <span style="opacity: 0.65;">NIK</span>
                        <strong style="font-weight: 700;">{{ $profile?->nik ?? '-' }}</strong>
                    </div>
                    <div class="info-row">
                        <span style="opacity: 0.65;">Email</span>
                        <strong style="font-weight: 600; word-break: break-all;">{{ $user->email }}</strong>
                    </div>
                    <div class="info-row">
                        <span style="opacity: 0.65;">Shift Kerja</span>
                        <div style="text-align: right;">
                            <strong style="display: block; font-weight: 700;">{{ $shift?->nama_shift ?? 'Shift Standar' }}</strong>
                            @if($shift)
                                <span style="font-size: 0.75rem; opacity: 0.75;">{{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }} WIB</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <span style="opacity: 0.65;">Hari Libur Rutin</span>
                        <strong style="font-weight: 700; color: #10b981; text-transform: capitalize;">
                            {{ $user->jadwal?->hari_libur_rutin ?? 'Minggu' }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Informasi -->
            <div class="profile-card" style="background: rgba(245, 158, 11, 0.05); border-color: rgba(245, 158, 11, 0.2);">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <svg style="width: 20px; height: 20px; color: #f59e0b; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div style="font-size: 0.75rem; line-height: 1.4; opacity: 0.85;">
                        <strong style="display: block; font-size: 0.8125rem; font-weight: 700; color: inherit; margin-bottom: 0.25rem;">Biometrik Wajah</strong>
                        Rekam foto wajah acuan master Anda dengan posisi tegak lurus dan penerangan yang baik agar kamera presensi mengenali Anda dengan cepat.
                    </div>
                </div>
            </div>

        </div>

        <!-- SISI KANAN: FORM REKAM WAJAH MASTER & EDIT PROFIL -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Section Biometrik: Pendaftaran Wajah Acuan Master -->
            <x-filament::section>
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; font-weight: 700;">
                        <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Perekaman Wajah Acuan (Biometrik Master)</span>
                    </div>
                </x-slot>

                <p style="font-size: 0.8125rem; opacity: 0.75; margin-bottom: 1rem;">
                    Foto wajah acuan ini menjadi data master agar sistem dapat memvalidasi bahwa yang melakukan presensi di toko adalah Anda.
                </p>

                @if($profile?->is_biometric_locked && $profile?->foto_wajah_acuan)
                    <!-- STATUS BIOMETRIK TERVERIFIKASI -->
                    <div style="padding: 1.25rem; border-radius: 0.875rem; border: 1.5px solid rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.08); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 1.25rem;">
                            <div style="position: relative;">
                                <img 
                                    src="{{ asset('storage/' . $profile->foto_wajah_acuan) }}" 
                                    alt="Foto Master" 
                                    style="width: 72px; height: 72px; border-radius: 0.625rem; object-fit: cover; border: 3px solid #10b981; box-shadow: 0 4px 10px -2px rgba(16, 185, 129, 0.35);"
                                >
                                <span style="position: absolute; bottom: -4px; right: -4px; width: 20px; height: 20px; background-color: #10b981; border: 2px solid white; border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: bold;">✓</span>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <strong style="font-size: 1rem; color: #059669;">Biometrik Wajah Terdaftar</strong>
                                    <span style="padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: #10b981; color: white;">
                                        Terverifikasi
                                    </span>
                                </div>
                                <div style="font-size: 0.8125rem; opacity: 0.85; margin-top: 0.25rem;">
                                    Didaftarkan: <strong>{{ $profile->wajah_terverifikasi_at ? \Carbon\Carbon::parse($profile->wajah_terverifikasi_at)->translatedFormat('l, d F Y H:i') . ' WIB' : 'Telah Terverifikasi' }}</strong>
                                </div>
                                <p style="font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0 0; max-width: 480px; line-height: 1.4;">
                                    Data biometrik wajah telah tersimpan. Hubungi administrator apabila Anda memerlukan pembaruan data biometrik.
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Terverifikasi
                            </span>
                        </div>
                    </div>
                @else
                    <!-- BELUM ADA MASTER -->
                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem; border-radius: 0.75rem; border: 1.5px dashed #f59e0b; background: rgba(245, 158, 11, 0.05);">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 64px; height: 64px; border-radius: 0.5rem; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #d97706; flex-shrink: 0;">
                                <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.375rem;">
                                    <span style="width: 8px; height: 8px; border-radius: 9999px; background: #f59e0b;"></span>
                                    <strong style="font-size: 0.875rem; color: #d97706;">Biometrik Wajah Belum Terdaftar</strong>
                                </div>
                                <span style="font-size: 0.75rem; opacity: 0.85; display: block; margin-top: 2px;">Silakan daftarkan foto wajah acuan Anda untuk keperluan verifikasi presensi mandiri.</span>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            id="btn-toggle-enroll-cam"
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; background: #f59e0b; color: white; border: none; cursor: pointer; transition: background 0.2s; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.35);"
                            onmouseover="this.style.backgroundColor='#d97706'"
                            onmouseout="this.style.backgroundColor='#f59e0b'"
                        >
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            <span>Buka Kamera & Ambil Foto Wajah</span>
                        </button>
                    </div>
                @endif

                <!-- Box Kamera Perekaman Wajah Master (Toggleable) -->
                <div id="enroll-cam-box" style="display: none; margin-top: 1.25rem; padding: 1.25rem; border-radius: 0.75rem; background: rgba(0, 0, 0, 0.03); border: 1px dashed #f59e0b;">
                    <div style="text-align: center; margin-bottom: 0.75rem;">
                        <strong style="display: block; font-size: 0.875rem;">Posisikan Wajah Tegak & Jelas</strong>
                        <span style="font-size: 0.75rem; opacity: 0.75;">Pastikan wajah Anda berada di dalam garis oval panduan kamera.</span>
                    </div>

                    <div class="enroll-viewport">
                        <video id="enroll-webcam" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                        <canvas id="enroll-canvas" style="display: none;"></canvas>
                        <div class="enroll-oval"></div>
                    </div>

                    <!-- Preview Foto Hasil Jepret -->
                    <div id="enroll-preview-box" style="display: none; text-align: center; margin-top: 1rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #059669; display: block; margin-bottom: 0.5rem;">Pratinjau Foto Wajah Master:</span>
                        <img id="enroll-preview-img" src="" style="width: 120px; height: 160px; border-radius: 0.5rem; object-fit: cover; margin: 0 auto; border: 2px solid #10b981; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </div>

                    <!-- Tombol Aksi Kamera -->
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem; margin-top: 1rem;">
                        <button 
                            type="button" 
                            id="btn-snap-enroll" 
                            style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: #f59e0b; color: white; border: none; cursor: pointer;"
                        >
                            📷 Jepret Foto
                        </button>
                        <button 
                            type="button" 
                            id="btn-save-enroll" 
                            style="display: none; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: #10b981; color: white; border: none; cursor: pointer;"
                        >
                            💾 Simpan Sebagai Wajah Master
                        </button>
                        <button 
                            type="button" 
                            id="btn-close-enroll" 
                            style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(156, 163, 175, 0.2); color: inherit; border: none; cursor: pointer;"
                        >
                            Tutup Kamera
                        </button>
                    </div>
                </div>
            </x-filament::section>

            <!-- Section Kontak & Password -->
            <form wire:submit.prevent="simpanProfil" style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Section 1: Biodata, Kontak & Berkas KTP -->
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; font-weight: 700;">
                            <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>Perbarui Data Kontak, Biodata & Dokumen Identitas</span>
                        </div>
                    </x-slot>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem; margin-top: 0.5rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">
                                    Nomor Induk Karyawan (NIK)
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="nik" 
                                    placeholder="Contoh: 12345678" 
                                    class="form-input-control"
                                >
                                @error('nik') 
                                    <span style="display: block; font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">
                                    Nomor Telepon / WhatsApp
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="telphone" 
                                    placeholder="Contoh: 081234567890" 
                                    class="form-input-control"
                                >
                                @error('telphone') 
                                    <span style="display: block; font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">
                                Alamat Domisili Lengkap
                            </label>
                            <textarea 
                                wire:model="alamat" 
                                rows="3" 
                                placeholder="Masukkan alamat domisili tempat tinggal Anda saat ini..." 
                                class="form-input-control"
                            ></textarea>
                            @error('alamat') 
                                <span style="display: block; font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Berkas KTP Terenkripsi Mandiri -->
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">
                                Dokumen KTP / Tanda Pengenal (Privasi Terenkripsi)
                            </label>

                            @if($profile?->foto_ktp)
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.875rem 1rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.08); border: 1.5px solid rgba(16, 185, 129, 0.3); margin-bottom: 0.625rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 32px; height: 32px; border-radius: 9999px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9375rem; font-weight: bold; flex-shrink: 0;">
                                            ✓
                                        </div>
                                        <div>
                                            <strong style="display: block; font-size: 0.875rem; color: #059669;">KTP Berhasil Diunggah & Terenkripsi Aman</strong>
                                            <span style="font-size: 0.75rem; color: #6b7280; line-height: 1.35; display: block; margin-top: 2px;">
                                                🔒 Berkas KTP Anda dienkripsi otomatis (AES-256). Demi privasi data Anda, Super Admin hanya dapat melihat status centang verifikasi tanpa dapat mengunduh/melihat fisik dokumen.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div style="margin-top: 0.25rem;">
                                <input 
                                    type="file" 
                                    wire:model="berkas_ktp" 
                                    accept="image/*,application/pdf"
                                    class="form-input-control"
                                    style="padding: 0.5rem;"
                                >
                                <span style="display: block; font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                                    {{ $profile?->foto_ktp ? 'Pilih berkas baru jika Anda ingin mengganti KTP yang tersimpan.' : 'Pilih berkas foto KTP (JPG, PNG, WEBP, atau PDF, maks 5MB). Berkas akan otomatis dienkripsi aman.' }}
                                </span>
                                @error('berkas_ktp') 
                                    <span style="display: block; font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>
                </x-filament::section>

                <!-- Section 2: Keamanan Akun & Password -->
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; font-weight: 700;">
                            <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Ganti Password Baru (Opsional)</span>
                        </div>
                    </x-slot>

                    <p style="font-size: 0.8125rem; opacity: 0.75; margin-bottom: 1rem;">
                        Biarkan input di bawah kosong jika Anda tidak berencana mengganti kata sandi.
                    </p>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">
                                    Password Baru
                                </label>
                                <input 
                                    type="password" 
                                    wire:model="password_baru" 
                                    placeholder="Minimal 6 karakter" 
                                    class="form-input-control"
                                >
                                @error('password_baru') 
                                    <span style="display: block; font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">
                                    Ulangi Password Baru
                                </label>
                                <input 
                                    type="password" 
                                    wire:model="konfirmasi_password" 
                                    placeholder="Ketik ulang password baru" 
                                    class="form-input-control"
                                >
                                @error('konfirmasi_password') 
                                    <span style="display: block; font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>
                </x-filament::section>

                <!-- Tombol Simpan -->
                <div style="display: flex; justify-content: flex-end; padding-top: 0.5rem;">
                    <button 
                        type="submit" 
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 700; background-color: #f59e0b; color: #ffffff; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.35); transition: background 0.2s;"
                        onmouseover="this.style.backgroundColor='#d97706'"
                        onmouseout="this.style.backgroundColor='#f59e0b'"
                    >
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan Profil
                    </button>
                </div>

            </form>

        </div>

    </div>

    <!-- Script Face-API & Kamera Rekam Wajah Master -->
    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        (function() {
            let isEnrollModelsLoaded = false;
            const ENROLL_MODEL_URL = '{{ asset('models') }}';

            async function loadEnrollModels() {
                if (window.faceapi && !isEnrollModelsLoaded) {
                    try {
                        await faceapi.nets.tinyFaceDetector.loadFromUri(ENROLL_MODEL_URL);
                        await faceapi.nets.faceLandmark68Net.loadFromUri(ENROLL_MODEL_URL);
                        await faceapi.nets.faceRecognitionNet.loadFromUri(ENROLL_MODEL_URL);
                        isEnrollModelsLoaded = true;
                    } catch(e) {
                        console.warn('Gagal memuat model face-api master:', e);
                    }
                }
            }

            function initEnrollCam() {
                loadEnrollModels();

                const btnToggle = document.getElementById('btn-toggle-enroll-cam');
                const boxCam = document.getElementById('enroll-cam-box');
                const video = document.getElementById('enroll-webcam');
                const canvas = document.getElementById('enroll-canvas');
                const btnSnap = document.getElementById('btn-snap-enroll');
                const btnSave = document.getElementById('btn-save-enroll');
                const btnClose = document.getElementById('btn-close-enroll');
                const previewBox = document.getElementById('enroll-preview-box');
                const previewImg = document.getElementById('enroll-preview-img');

                let streamInstance = null;
                let capturedBase64 = null;
                let capturedDescriptor = null;

                if (!btnToggle || !boxCam) return;

                btnToggle.onclick = function() {
                    boxCam.style.display = 'block';
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                            audio: false
                        }).then(stream => {
                            streamInstance = stream;
                            video.srcObject = stream;
                        }).catch(err => {
                            console.error('Kamera enroll error:', err);
                        }).catch(err => {
                            console.error('Kamera enroll error:', err);
                            showAppModal('Gagal Mengakses Kamera', 'Pastikan izin kamera telah diaktifkan di peramban Anda. Klik ikon gembok 🔒 pada address bar jika kamera diblokir.', 'error');
                        });
                    }
                };

                if (btnSnap) {
                    btnSnap.onclick = async function() {
                        btnSnap.disabled = true;
                        btnSnap.innerText = 'Menganalisis Biometrik...';

                        // Ekstraksi 128-vektor deskriptor biometrik wajah
                        capturedDescriptor = null;
                        capturedBase64 = null;
                        btnSave.style.display = 'none';
                        previewBox.style.display = 'none';

                        if (!window.faceapi || !isEnrollModelsLoaded) {
                            btnSnap.disabled = false;
                            btnSnap.innerText = '📷 Jepret Foto';
                            showAppModal('Model Biometrik Sedang Dimuat', 'Model AI pendeteksi wajah sedang dimuat. Harap tunggu beberapa saat kemudian coba lagi.', 'warning');
                            return;
                        }

                        try {
                            const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.75 });
                            const detection = await faceapi.detectSingleFace(video, options)
                                .withFaceLandmarks()
                                .withFaceDescriptor();

                            if (!detection || detection.detection.score < 0.75) {
                                btnSnap.disabled = false;
                                btnSnap.innerText = '📷 Jepret Ulang';
                                showAppModal('Wajah Tidak Terdeteksi Jelas', 'Wajah tidak terdeteksi dengan jelas pada kamera. Pastikan pencahayaan cukup terang, tidak membelakangi cahaya, dan pandang langsung ke kamera.', 'warning');
                                return;
                            }

                            const videoW = video.videoWidth || 640;
                            const videoH = video.videoHeight || 480;
                            const centerX = videoW / 2;
                            const centerY = videoH / 2;
                            const box = detection.detection.box;

                            // 1. Validasi Batas Tepi (Wajah Tidak Boleh Terpotong Tepi Kamera)
                            const marginX = videoW * 0.08;
                            const marginY = videoH * 0.06;
                            const isCutOff = (box.x < marginX) || 
                                            (box.y < marginY) || 
                                            ((box.x + box.width) > (videoW - marginX)) || 
                                            ((box.y + box.height) > (videoH - marginY));

                            if (isCutOff) {
                                btnSnap.disabled = false;
                                btnSnap.innerText = '📷 Jepret Ulang';
                                showAppModal('Posisi Wajah Terpotong', 'Wajah Anda berada terlalu di tepi kamera sehingga terpotong. Posisikan seluruh wajah dan dagu Anda utuh di tengah area oval kamera.', 'warning');
                                return;
                            }

                            // 2. Validasi Titik Tengah
                            const faceX = box.x + box.width / 2;
                            const faceY = box.y + box.height / 2;
                            const diffX = Math.abs(faceX - centerX);
                            const diffY = Math.abs(faceY - centerY);
                            const isCentered = (diffX <= videoW * 0.20) && (diffY <= videoH * 0.22);

                            if (!isCentered) {
                                btnSnap.disabled = false;
                                btnSnap.innerText = '📷 Jepret Ulang';
                                showAppModal('Wajah Kurang ke Tengah', 'Posisikan wajah Anda tepat di bagian tengah panduan oval kamera.', 'warning');
                                return;
                            }

                            // 3. Validasi Proporsi Ukuran Wajah
                            const isProperSize = (box.width >= videoW * 0.25) && (box.width <= videoW * 0.68) && (box.height >= videoH * 0.28);
                            if (!isProperSize) {
                                btnSnap.disabled = false;
                                btnSnap.innerText = '📷 Jepret Ulang';
                                showAppModal(
                                    box.width < videoW * 0.25 ? 'Wajah Terlalu Jauh' : 'Wajah Terlalu Dekat',
                                    box.width < videoW * 0.25 ? 'Dekatkan wajah Anda ke kamera agar fitur wajah dapat terbaca jelas.' : 'Mundurkan posisi wajah Anda sedikit dari kamera.',
                                    'warning'
                                );
                                return;
                            }

                            // 4. Validasi Kesimetrisan (Menghadap Lurus) & Kemiringan Kepala (Upright)
                            const landmarks = detection.landmarks;
                            const jaw = landmarks.getJawOutline();
                            const nose = landmarks.getNose();
                            const leftEye = landmarks.getLeftEye();
                            const rightEye = landmarks.getRightEye();

                            const noseTip = nose[3] || nose[0];
                            const leftJaw = jaw[2] || jaw[0];
                            const rightJaw = jaw[14] || jaw[16];
                            const distLeft = Math.hypot(noseTip.x - leftJaw.x, noseTip.y - leftJaw.y);
                            const distRight = Math.hypot(rightJaw.x - noseTip.x, rightJaw.y - noseTip.y);
                            const symmetryRatio = distLeft / (distRight || 1);
                            const isFacingFront = (symmetryRatio >= 0.65) && (symmetryRatio <= 1.50);

                            const eyeDeltaY = Math.abs(leftEye[0].y - rightEye[3].y);
                            const eyeDeltaX = Math.abs(leftEye[0].x - rightEye[3].x);
                            const tiltRatio = eyeDeltaY / (eyeDeltaX || 1);
                            const isUpright = (tiltRatio <= 0.22);

                            if (!isFacingFront) {
                                btnSnap.disabled = false;
                                btnSnap.innerText = '📷 Jepret Ulang';
                                showAppModal('Hadapkan Wajah Lurus', 'Wajah Anda menoleh ke samping. Pastikan Anda menghadap lurus ke depan ke arah kamera.', 'warning');
                                return;
                            }

                            if (!isUpright) {
                                btnSnap.disabled = false;
                                btnSnap.innerText = '📷 Jepret Ulang';
                                showAppModal('Posisikan Kepala Tegak', 'Kepala Anda dalam posisi miring. Tegakkan kepala Anda sejajar dengan kamera.', 'warning');
                                return;
                            }

                            // Seluruh Validasi Ketat Lolos: Simpan Frame & Deskriptor Biometrik
                            canvas.width = 360;
                            canvas.height = 480;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(video, 0, 0, 360, 480);
                            capturedBase64 = canvas.toDataURL('image/jpeg', 0.88);
                            capturedDescriptor = JSON.stringify(Array.from(detection.descriptor));

                            previewImg.src = capturedBase64;
                            previewBox.style.display = 'block';
                            btnSave.style.display = 'inline-flex';

                            showAppModal('Wajah Berhasil Dipindai', 'Posisi wajah Anda tegak, jelas, dan memenuhi standar biometrik. Klik tombol "Simpan Sebagai Wajah Master" di bawah.', 'success');

                        } catch(e) {
                            console.warn('Gagal analisis biometrik:', e);
                            showAppModal('Kesalahan Analisis', 'Terjadi kesalahan saat memproses fitur biometrik wajah. Silakan coba lagi.', 'error');
                        } finally {
                            btnSnap.disabled = false;
                            btnSnap.innerText = '📷 Jepret Ulang';
                        }
                    };
                }

                if (btnSave) {
                    btnSave.onclick = function() {
                        if (!capturedBase64 || !capturedDescriptor) {
                            showAppModal('Wajah Belum Terverifikasi', 'Silakan jepret foto dengan posisi wajah tegak dan jelas sebelum menyimpan.', 'warning');
                            return;
                        }
                        btnSave.disabled = true;
                        btnSave.innerText = 'Menyimpan Biometrik...';

                        // Panggil Livewire method simpanFotoWajahMaster beserta vektor biometrik
                        @this.simpanFotoWajahMaster(capturedBase64, capturedDescriptor).then(() => {
                            if (streamInstance) {
                                streamInstance.getTracks().forEach(track => track.stop());
                            }
                            boxCam.style.display = 'none';
                            showAppModal('Biometrik Disimpan', 'Data wajah acuan Anda berhasil didaftarkan dan siap digunakan untuk presensi.', 'success', () => {
                                window.location.reload();
                            });
                        });
                    };
                }

                if (btnClose) {
                    btnClose.onclick = function() {
                        if (streamInstance) {
                            streamInstance.getTracks().forEach(track => track.stop());
                        }
                        boxCam.style.display = 'none';
                        previewBox.style.display = 'none';
                        btnSave.style.display = 'none';
                    };
                }
            }

            function showAppModal(title, message, iconType = 'error', onClose = null) {
                const overlay = document.getElementById('app-modal-overlay');
                const card = document.getElementById('app-modal-card');
                const titleEl = document.getElementById('app-modal-title');
                const msgEl = document.getElementById('app-modal-message');
                const iconBox = document.getElementById('app-modal-icon-container');
                const btnCloseModal = document.getElementById('app-modal-btn-close');

                if (!overlay || !card) {
                    window.alert(title + "\n\n" + message);
                    if (typeof onClose === 'function') onClose();
                    return;
                }

                titleEl.innerText = title;
                msgEl.innerText = message;

                if (iconType === 'success') {
                    iconBox.style.background = 'rgba(16, 185, 129, 0.15)';
                    iconBox.style.color = '#059669';
                    iconBox.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
                    btnCloseModal.style.background = '#10b981';
                    btnCloseModal.innerText = 'Selesai';
                } else if (iconType === 'warning') {
                    iconBox.style.background = 'rgba(245, 158, 11, 0.15)';
                    iconBox.style.color = '#d97706';
                    iconBox.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                    btnCloseModal.style.background = '#f59e0b';
                    btnCloseModal.innerText = 'Mengerti';
                } else {
                    iconBox.style.background = 'rgba(239, 68, 68, 0.15)';
                    iconBox.style.color = '#dc2626';
                    iconBox.innerHTML = '<svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    btnCloseModal.style.background = '#ef4444';
                    btnCloseModal.innerText = 'Tutup';
                }

                overlay.style.display = 'flex';
                requestAnimationFrame(() => {
                    card.style.transform = 'scale(1)';
                    card.style.opacity = '1';
                });

                btnCloseModal.onclick = function() {
                    card.style.transform = 'scale(0.9)';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        overlay.style.display = 'none';
                        if (typeof onClose === 'function') onClose();
                    }, 180);
                };
            }

            document.addEventListener('DOMContentLoaded', initEnrollCam);
            document.addEventListener('livewire:navigated', initEnrollCam);
        })();
    </script>

    <!-- MODAL WEB IN-APP UNTUK PEMBERITAHUAN BIOMETRIK -->
    <div id="app-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1.25rem;">
        <div id="app-modal-card" style="background: #ffffff; border-radius: 1.25rem; width: 100%; max-width: 400px; padding: 1.75rem 1.5rem; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.35); transform: scale(0.9); opacity: 0; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
            <div id="app-modal-icon-container" style="width: 56px; height: 56px; border-radius: 9999px; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;"></div>
            <h3 id="app-modal-title" style="font-size: 1.15rem; font-weight: 800; margin: 0 0 0.5rem 0; color: #111827;"></h3>
            <p id="app-modal-message" style="font-size: 0.875rem; line-height: 1.5; margin: 0 0 1.5rem 0; color: #4b5563;"></p>
            <button type="button" id="app-modal-btn-close" style="width: 100%; padding: 0.75rem 1.25rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 700; color: #ffffff; border: none; cursor: pointer; transition: opacity 0.15s;">
                Mengerti
            </button>
        </div>
    </div>
</x-filament-panels::page>
