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

                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.25); background: rgba(156, 163, 175, 0.05);">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        @if($profile?->foto_wajah_acuan && file_exists(public_path('storage/' . $profile->foto_wajah_acuan)))
                            <img 
                                src="{{ asset('storage/' . $profile->foto_wajah_acuan) }}" 
                                alt="Foto Master" 
                                style="width: 64px; height: 64px; border-radius: 0.5rem; object-fit: cover; border: 2px solid #10b981;"
                            >
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.375rem;">
                                    <span style="width: 8px; height: 8px; border-radius: 9999px; background: #10b981;"></span>
                                    <strong style="font-size: 0.875rem; color: #059669;">Wajah Acuan Master Terdaftar</strong>
                                </div>
                                <span style="font-size: 0.75rem; opacity: 0.75; display: block; margin-top: 2px;">Data wajah siap digunakan untuk verifikasi presensi harian.</span>
                            </div>
                        @else
                            <div style="width: 64px; height: 64px; border-radius: 0.5rem; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #d97706; flex-shrink: 0;">
                                <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.375rem;">
                                    <span style="width: 8px; height: 8px; border-radius: 9999px; background: #f59e0b;"></span>
                                    <strong style="font-size: 0.875rem; color: #d97706;">Wajah Master Belum Didaftarkan</strong>
                                </div>
                                <span style="font-size: 0.75rem; opacity: 0.75; display: block; margin-top: 2px;">Wajib rekam wajah bagi karyawan baru sebelum mulai presensi.</span>
                            </div>
                        @endif
                    </div>

                    <button 
                        type="button" 
                        id="btn-toggle-enroll-cam"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: #f59e0b; color: white; border: none; cursor: pointer; transition: background 0.2s;"
                        onmouseover="this.style.backgroundColor='#d97706'"
                        onmouseout="this.style.backgroundColor='#f59e0b'"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <span>{{ $profile?->foto_wajah_acuan ? 'Rekam Ulang Wajah Master' : 'Buka Kamera & Rekam Wajah' }}</span>
                    </button>
                </div>

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
                
                <!-- Section 1: Data Kontak -->
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; font-weight: 700;">
                            <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>Perbarui Data Kontak & Domisili</span>
                        </div>
                    </x-slot>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem; margin-top: 0.5rem;">
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

    <!-- Script Kamera Rekam Wajah Master -->
    <script>
        (function() {
            function initEnrollCam() {
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
                            alert('Gagal mengakses kamera. Pastikan izin kamera telah diaktifkan di browser.');
                        });
                    }
                };

                if (btnSnap) {
                    btnSnap.onclick = function() {
                        canvas.width = 360;
                        canvas.height = 480;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, 360, 480);
                        capturedBase64 = canvas.toDataURL('image/jpeg', 0.85);

                        previewImg.src = capturedBase64;
                        previewBox.style.display = 'block';
                        btnSave.style.display = 'inline-flex';
                    };
                }

                if (btnSave) {
                    btnSave.onclick = function() {
                        if (!capturedBase64) return;
                        btnSave.disabled = true;
                        btnSave.innerText = 'Menyimpan...';

                        // Panggil Livewire method simpanFotoWajahMaster
                        @this.simpanFotoWajahMaster(capturedBase64).then(() => {
                            if (streamInstance) {
                                streamInstance.getTracks().forEach(track => track.stop());
                            }
                            boxCam.style.display = 'none';
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

            document.addEventListener('DOMContentLoaded', initEnrollCam);
            document.addEventListener('livewire:navigated', initEnrollCam);
        })();
    </script>
</x-filament-panels::page>
