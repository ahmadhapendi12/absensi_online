<x-filament-widgets::widget>
    <x-filament::section class="overflow-hidden">
        {{-- Scoped CSS to ensure 100% styled layout under all browser conditions --}}
        <style>
            .karyawan-banner {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            @media (min-width: 1024px) {
                .karyawan-banner {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                }
            }
            .karyawan-profile-info {
                display: flex;
                align-items: center;
                gap: 1.25rem;
            }
            @media (max-width: 480px) {
                .karyawan-profile-info {
                    gap: 0.875rem;
                }
                .karyawan-avatar-img {
                    width: 64px;
                    height: 64px;
                }
            }
            .karyawan-avatar-box {
                position: relative;
                flex-shrink: 0;
            }
            .karyawan-avatar-img {
                width: 72px;
                height: 72px;
                border-radius: 9999px;
                object-fit: cover;
                border: 3px solid #f59e0b;
                box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);
            }
            .karyawan-status-box {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                background: rgba(245, 158, 11, 0.06);
                border: 1px solid rgba(245, 158, 11, 0.2);
                border-radius: 1rem;
                padding: 1.25rem;
                min-width: 280px;
            }
            .karyawan-status-box.active-work {
                background: rgba(16, 185, 129, 0.06);
                border-color: rgba(16, 185, 129, 0.25);
            }
            .karyawan-status-box.done-work {
                background: rgba(99, 102, 241, 0.06);
                border-color: rgba(99, 102, 241, 0.25);
            }
            .quick-actions-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                margin: 1.25rem auto 0 auto;
                padding-top: 1.25rem;
                border-top: 1px solid rgba(156, 163, 175, 0.2);
                width: 100%;
            }
            @media (min-width: 768px) {
                .quick-actions-grid {
                    grid-template-columns: repeat(4, 1fr);
                    gap: 1rem;
                }
            }
            .action-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                gap: 0.5rem;
                padding: 0.875rem 0.5rem;
                border-radius: 0.875rem;
                border: 1px solid rgba(156, 163, 175, 0.2);
                background: rgba(255, 255, 255, 0.6);
                text-decoration: none;
                transition: all 0.2s ease-in-out;
                width: 100%;
                box-sizing: border-box;
            }
            @media (min-width: 640px) {
                .action-card {
                    flex-direction: row;
                    text-align: left;
                    align-items: center;
                    justify-content: flex-start;
                    gap: 0.75rem;
                    padding: 0.875rem 1rem;
                }
            }
            .action-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.08);
                border-color: #f59e0b;
            }
            .dark .action-card {
                background: rgba(31, 41, 55, 0.6);
                border-color: rgba(75, 85, 99, 0.4);
            }
            .dark .action-card:hover {
                border-color: #f59e0b;
                box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.4);
            }
        </style>

        <div class="karyawan-banner">
            <!-- Profil & Shift Karyawan -->
            <div class="karyawan-profile-info">
                <div class="karyawan-avatar-box">
                    <img 
                        src="{{ $user->avatar_url }}" 
                        alt="{{ $user->name }}" 
                        class="karyawan-avatar-img"
                        width="72"
                        height="72"
                    >
                    <span style="position: absolute; bottom: 2px; right: 2px; width: 14px; height: 14px; background-color: #10b981; border: 2px solid white; border-radius: 9999px;"></span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #f59e0b;">Portal Karyawan</span>
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                            {{ $user->cabang?->nama_cabang ?? 'Kantor Pusat' }}
                        </span>
                    </div>

                    <h1 style="font-size: 1.5rem; font-weight: 800; line-height: 1.2; margin: 0; color: inherit;">
                        {{ $user->name }}
                    </h1>

                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; font-size: 0.8125rem; opacity: 0.85;">
                        <span>NIK: <strong>{{ $user->profile?->nik ?? '-' }}</strong></span>
                        <span>&bull;</span>
                        <span>{{ $hariIniFormatted }}</span>
                    </div>

                    <!-- Shift Info Badge -->
                    <div style="display: inline-flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.25rem; font-size: 0.8125rem;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 9999px; background-color: #10b981;"></span>
                        <span>Shift: <strong>{{ $shift?->nama_shift ?? 'Belum Dipilih' }}</strong></span>
                        @if($shift)
                            <span style="padding: 0.125rem 0.375rem; border-radius: 0.25rem; background: rgba(156, 163, 175, 0.15); font-weight: 600;">
                                {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }} WIB
                            </span>
                        @endif
                        <a href="{{ url('/karyawan/pilih-shift') }}" style="font-size: 0.75rem; color: #f59e0b; text-decoration: underline; font-weight: 700; margin-left: 0.25rem;">
                            (Pilih / Ganti Shift)
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Presensi & Action Callout -->
            <div class="karyawan-status-box @if($absensi && $absensi->jam_masuk && !$absensi->jam_pulang) active-work @elseif($absensi && $absensi->jam_pulang) done-work @endif">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        @if(!$absensi)
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 9999px; background-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25);"></span>
                            <span style="font-size: 0.875rem; font-weight: 700; color: #b45309;">Belum Presensi Masuk</span>
                        @elseif($absensi->status_masuk === 'Libur')
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 9999px; background-color: #6b7280; box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.25);"></span>
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 700; color: #4b5563;">Status: Libur</div>
                                <div style="font-size: 0.75rem; opacity: 0.85;">Tidak bertugas / di luar jam operasional shift</div>
                            </div>
                        @elseif(in_array($absensi->status_masuk, ['Izin', 'Cuti', 'Sakit']))
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 9999px; background-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);"></span>
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 700; color: #1d4ed8;">Status: {{ $absensi->status_masuk }}</div>
                                <div style="font-size: 0.75rem; opacity: 0.85;">Pengajuan izin telah disetujui</div>
                            </div>
                        @elseif($absensi->jam_masuk && !$absensi->jam_pulang)
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 9999px; background-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);"></span>
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 700; color: #047857;">Sedang Bekerja</div>
                                <div style="font-size: 0.75rem; color: #065f46;">Masuk: {{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }} ({{ $absensi->status_masuk }})</div>
                            </div>
                        @else
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 9999px; background-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);"></span>
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 700; color: #4338ca;">Presensi Hari Ini Selesai</div>
                                <div style="font-size: 0.75rem; opacity: 0.85;">{{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }} WIB</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Navigasi Presensi -->
                <div>
                    @if(!$absensi)
                        <a 
                            href="{{ url('/karyawan/presensi-mandiri') }}" 
                            style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.625rem 1rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 700; background-color: #f59e0b; color: #ffffff; text-decoration: none; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3); transition: background 0.2s;"
                            onmouseover="this.style.backgroundColor='#d97706'"
                            onmouseout="this.style.backgroundColor='#f59e0b'"
                        >
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Ambil Presensi Masuk
                        </a>
                    @elseif($absensi->status_masuk === 'Libur' || in_array($absensi->status_masuk, ['Izin', 'Cuti', 'Sakit']))
                        <a 
                            href="{{ url('/karyawan/rekap-presensis/rekap-presensi-karyawans') }}" 
                            style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.625rem 1rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 600; background-color: rgba(156, 163, 175, 0.2); color: inherit; text-decoration: none; transition: background 0.2s;"
                        >
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Lihat Rekap Presensi
                        </a>
                    @elseif($absensi->jam_masuk && !$absensi->jam_pulang)
                        <a 
                            href="{{ url('/karyawan/presensi-mandiri') }}" 
                            style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.625rem 1rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 700; background-color: #ef4444; color: #ffffff; text-decoration: none; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); transition: background 0.2s;"
                            onmouseover="this.style.backgroundColor='#dc2626'"
                            onmouseout="this.style.backgroundColor='#ef4444'"
                        >
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Ambil Presensi Pulang
                        </a>
                    @else
                        <a 
                            href="{{ url('/karyawan/rekap-presensis/rekap-presensi-karyawans') }}" 
                            style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.625rem 1rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 600; background-color: rgba(156, 163, 175, 0.2); color: inherit; text-decoration: none; transition: background 0.2s;"
                        >
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Lihat Rekap Presensi
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions Row -->
        <div class="quick-actions-grid">
            <a href="{{ url('/karyawan/presensi-mandiri') }}" class="action-card">
                <div style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 0.5rem; background: rgba(245, 158, 11, 0.15); color: #f59e0b; flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: inherit;">Presensi Online</div>
                    <div style="font-size: 0.75rem; opacity: 0.7;">Kamera & GPS</div>
                </div>
            </a>

            <a href="{{ url('/karyawan/pengajuan-izins/pengajuan-izin-karyawans/create') }}" class="action-card">
                <div style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 0.5rem; background: rgba(59, 130, 246, 0.15); color: #3b82f6; flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: inherit;">Ajukan Izin / Cuti</div>
                    <div style="font-size: 0.75rem; opacity: 0.7;">Sakit, cuti & urusan</div>
                </div>
            </a>

            <a href="{{ url('/karyawan/lemburs/lembur-karyawans/create') }}" class="action-card">
                <div style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 0.5rem; background: rgba(168, 85, 247, 0.15); color: #a855f7; flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: inherit;">Ajukan Lembur</div>
                    <div style="font-size: 0.75rem; opacity: 0.7;">Catat jam lembur</div>
                </div>
            </a>

            <a href="{{ url('/karyawan/rekap-presensis/rekap-presensi-karyawans') }}" class="action-card">
                <div style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.15); color: #10b981; flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: inherit;">Rekap Kehadiran</div>
                    <div style="font-size: 0.75rem; opacity: 0.7;">Riwayat & PDF</div>
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
