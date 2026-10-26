<x-filament-panels::page>
    <style>
        .shift-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
        }
        @media (min-width: 768px) {
            .shift-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1200px) {
            .shift-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        .shift-card {
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            border: 1.5px solid rgba(156, 163, 175, 0.25);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease-in-out;
        }
        .shift-card:hover {
            border-color: rgba(245, 158, 11, 0.45);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
        }
        .shift-card.active {
            border: 2px solid #10b981;
            background: rgba(16, 185, 129, 0.04);
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);
        }
        .dark .shift-card {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(75, 85, 99, 0.4);
        }
        .dark .shift-card.active {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.08);
        }
        .badge-lintas-hari {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.6875rem;
            font-weight: 700;
            background: rgba(99, 102, 241, 0.15);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.25);
        }
        .dark .badge-lintas-hari {
            background: rgba(99, 102, 241, 0.25);
            color: #818cf8;
        }
        .shift-time-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(156, 163, 175, 0.08);
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            margin: 0.5rem 0;
        }
        .dark .shift-time-display {
            background: rgba(255, 255, 255, 0.04);
        }
    </style>

    <!-- Header Banner: Informasi Toko / Cabang & Fleksibilitas Shift -->
    <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(59, 130, 246, 0.08) 100%); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 44px; height: 44px; border-radius: 0.75rem; background: #f59e0b; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.35);">
                        🏪
                    </div>
                    <div>
                        <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0; line-height: 1.2;">
                            {{ $cabang?->nama_cabang ?? 'Cabang Pusat' }}
                        </h2>
                        <span style="font-size: 0.8125rem; opacity: 0.75;">
                            Kode: {{ $cabang?->kode_cabang ?? '-' }} &bull; {{ $cabang?->alamat ?? 'Alamat toko' }}
                        </span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    @if($cabang?->is_24_jam)
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.35);">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 9999px; background: #10b981; animation: pulse 2s infinite;"></span>
                            Toko Operasional 24 Jam
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(156, 163, 175, 0.15); color: inherit; border: 1px solid rgba(156, 163, 175, 0.3);">
                            Jam Operasional Reguler
                        </span>
                    @endif
                </div>
            </div>

            <p style="font-size: 0.875rem; opacity: 0.85; margin: 0; line-height: 1.5;">
                Pilih shift kerja aktif Anda sesuai kesepakatan tugas toko. Jam presensi masuk dan pulang pada kamera biometrik akan otomatis disesuaikan dengan shift yang Anda pilih di sini.
            </p>
        </div>
    </div>

    <!-- Status Shift Karyawan Saat Ini -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <svg style="width: 22px; height: 22px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Daftar Shift Tersedia untuk Cabang Anda</h3>
        </div>

        @if($activeShift)
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.875rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.8125rem;">
                <span style="opacity: 0.75;">Shift Aktif:</span>
                <strong style="color: #059669; font-weight: 800;">{{ $activeShift->nama_shift }}</strong>
                <span>({{ \Carbon\Carbon::parse($activeShift->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($activeShift->jam_pulang)->format('H:i') }} WIB)</span>
            </div>
        @else
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.875rem; border-radius: 0.5rem; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #dc2626; font-size: 0.8125rem; font-weight: 700;">
                ⚠️ Anda belum memilih shift aktif. Silakan pilih salah satu di bawah ini.
            </div>
        @endif
    </div>

    @if(isset($sedangBekerja) && $sedangBekerja)
        <div style="background: rgba(239, 68, 68, 0.08); border: 1.5px solid rgba(239, 68, 68, 0.35); border-radius: 0.875rem; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1.5rem;">
            <div style="font-size: 1.5rem;">🔒</div>
            <div>
                <strong style="color: #dc2626; font-size: 0.9375rem; display: block;">Perubahan Shift Sedang Dikunci</strong>
                <span style="font-size: 0.8125rem; color: #b91c1c;">
                    Anda telah melakukan presensi masuk dan sedang bekerja pada shift aktif hari ini. Pindah shift tidak diizinkan di tengah jam kerja. Silakan lakukan presensi pulang terlebih dahulu setelah shift Anda berakhir.
                </span>
            </div>
        </div>
    @endif

    <!-- Grid Shift Cards -->
    @if($shifts->isEmpty())
        <div style="text-align: center; padding: 3rem 1.5rem; background: rgba(156, 163, 175, 0.06); border-radius: 1rem; border: 1px dashed rgba(156, 163, 175, 0.3);">
            <svg style="width: 48px; height: 48px; margin: 0 auto 1rem auto; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h4 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.5rem 0;">Belum Ada Shift Dikonfigurasi</h4>
            <p style="font-size: 0.8125rem; opacity: 0.75; margin: 0;">Silakan hubungi Super Admin untuk menambahkan shift khusus cabang ini.</p>
        </div>
    @else
        <div class="shift-grid">
            @foreach($shifts as $s)
                @php 
                    $isActive = ($activeShiftId === $s->id); 
                @endphp
                <div class="shift-card {{ $isActive ? 'active' : '' }}">
                    <div>
                        <!-- Header Card -->
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div>
                                <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 0.25rem 0; color: {{ $isActive ? '#10b981' : 'inherit' }};">
                                    {{ $s->nama_shift }}
                                </h4>
                                <span style="font-size: 0.6875rem; opacity: 0.7; font-weight: 600;">
                                    {{ $s->cabang ? 'Khusus ' . $s->cabang->nama_cabang : 'Shift Umum / Semua Cabang' }}
                                </span>
                            </div>

                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.375rem;">
                                @if($isActive)
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 800; background: #10b981; color: white;">
                                        ✓ Aktif
                                    </span>
                                @endif

                                @if($s->lintas_hari)
                                    <span class="badge-lintas-hari">
                                        🌙 Lintas Hari
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Jam Masuk & Pulang -->
                        <div class="shift-time-display">
                            <div>
                                <span style="display: block; font-size: 0.6875rem; opacity: 0.65; text-transform: uppercase; font-weight: 700;">Jam Masuk</span>
                                <strong style="font-size: 1.25rem; font-weight: 800;">
                                    {{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }}
                                </strong>
                                <span style="font-size: 0.6875rem; opacity: 0.65;">WIB</span>
                            </div>

                            <div style="font-size: 1.25rem; opacity: 0.35;">→</div>

                            <div style="text-align: right;">
                                <span style="display: block; font-size: 0.6875rem; opacity: 0.65; text-transform: uppercase; font-weight: 700;">Jam Pulang</span>
                                <strong style="font-size: 1.25rem; font-weight: 800;">
                                    {{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }}
                                </strong>
                                <span style="font-size: 0.6875rem; opacity: 0.65;">WIB</span>
                            </div>
                        </div>

                        <!-- Keterangan & Detail -->
                        <div style="display: flex; flex-direction: column; gap: 0.375rem; font-size: 0.75rem; margin-top: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; opacity: 0.8;">
                                <span>Toleransi Keterlambatan:</span>
                                <strong>{{ $s->toleransi_terlambat_menit ?? 15 }} Menit</strong>
                            </div>
                            @if($s->keterangan)
                                <div style="font-size: 0.75rem; opacity: 0.7; font-style: italic; margin-top: 0.25rem;">
                                    "{{ $s->keterangan }}"
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Aksi Pilih Shift -->
                    <div style="margin-top: 0.5rem;">
                        @if($isActive)
                            <div style="width: 100%; padding: 0.625rem; border-radius: 0.625rem; font-size: 0.8125rem; font-weight: 800; background: rgba(16, 185, 129, 0.15); color: #059669; text-align: center; border: 1px solid rgba(16, 185, 129, 0.3);">
                                ✓ Shift Pilihan Anda Saat Ini
                            </div>
                        @elseif(isset($sedangBekerja) && $sedangBekerja)
                            <div style="width: 100%; padding: 0.625rem; border-radius: 0.625rem; font-size: 0.8125rem; font-weight: 700; background: rgba(156, 163, 175, 0.12); color: #9ca3af; text-align: center; border: 1px dashed rgba(156, 163, 175, 0.3); cursor: not-allowed;">
                                🔒 Terkunci (Sedang Bekerja)
                            </div>
                        @else
                            <button 
                                type="button" 
                                wire:click="pilihShift('{{ $s->id }}')"
                                wire:loading.attr="disabled"
                                wire:target="pilihShift"
                                style="width: 100%; padding: 0.625rem; border-radius: 0.625rem; font-size: 0.8125rem; font-weight: 700; background: #f59e0b; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.25); transition: opacity 0.2s;"
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'"
                            >
                                <span wire:loading.remove wire:target="pilihShift('{{ $s->id }}')">Pilih Shift Ini →</span>
                                <span wire:loading wire:target="pilihShift('{{ $s->id }}')">Menyimpan...</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
