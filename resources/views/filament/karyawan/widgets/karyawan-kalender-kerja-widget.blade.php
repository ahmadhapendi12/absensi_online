<x-filament-widgets::widget>
    <x-filament::section>
        <style>
            .gcal-container {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
                font-family: inherit;
            }
            .gcal-header {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid rgba(156, 163, 175, 0.2);
            }
            .gcal-nav-group {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .gcal-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.35rem;
                padding: 0.45rem 0.85rem;
                border-radius: 0.5rem;
                font-size: 0.8125rem;
                font-weight: 700;
                background: rgba(156, 163, 175, 0.1);
                color: inherit;
                border: 1px solid rgba(156, 163, 175, 0.25);
                cursor: pointer;
                transition: all 0.15s ease-in-out;
            }
            .gcal-btn:hover {
                background: rgba(245, 158, 11, 0.15);
                border-color: #f59e0b;
                color: #d97706;
            }
            .gcal-title {
                font-size: 1.35rem;
                font-weight: 800;
                letter-spacing: -0.025em;
                margin: 0;
            }
            .gcal-legend {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.75rem;
            }
            .gcal-legend-item {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.2rem 0.5rem;
                border-radius: 0.375rem;
                font-weight: 600;
            }
            .gcal-grid {
                display: grid;
                grid-template-columns: repeat(7, minmax(0, 1fr));
                border: 1px solid rgba(156, 163, 175, 0.25);
                border-radius: 0.75rem;
                overflow: hidden;
                background: rgba(255, 255, 255, 0.5);
            }
            .dark .gcal-grid {
                background: rgba(17, 24, 39, 0.5);
                border-color: rgba(75, 85, 99, 0.4);
            }
            .gcal-weekday {
                padding: 0.625rem 0.25rem;
                text-align: center;
                font-size: 0.75rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                background: rgba(156, 163, 175, 0.08);
                border-bottom: 1px solid rgba(156, 163, 175, 0.2);
                border-right: 1px solid rgba(156, 163, 175, 0.15);
            }
            .gcal-weekday:last-child {
                border-right: none;
            }
            .gcal-cell {
                min-height: 96px;
                padding: 0.4rem;
                border-bottom: 1px solid rgba(156, 163, 175, 0.15);
                border-right: 1px solid rgba(156, 163, 175, 0.15);
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
                cursor: pointer;
                transition: background 0.15s ease-in-out;
            }
            .gcal-cell:nth-child(7n) {
                border-right: none;
            }
            .gcal-cell:hover {
                background: rgba(245, 158, 11, 0.06);
            }
            .gcal-cell-dimmed {
                opacity: 0.35;
                background: rgba(156, 163, 175, 0.03);
            }
            .gcal-cell-pre-register {
                opacity: 0.45;
                background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(156, 163, 175, 0.03) 10px, rgba(156, 163, 175, 0.03) 20px);
            }
            .gcal-cell-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 0.15rem;
            }
            .gcal-day-num {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 700;
            }
            .gcal-today-pill {
                background: #f59e0b;
                color: #ffffff !0important;
                box-shadow: 0 2px 4px rgba(245, 158, 11, 0.4);
            }
            .gcal-event-pill {
                padding: 0.15rem 0.35rem;
                border-radius: 0.25rem;
                font-size: 0.6875rem;
                font-weight: 600;
                line-height: 1.25;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }
            .pill-primary { background: rgba(59, 130, 246, 0.15); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.3); }
            .pill-amber { background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3); }
            .pill-danger { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); }
            .pill-warning { background: rgba(245, 158, 11, 0.18); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.35); }
            .pill-emerald { background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); }
            .pill-success { background: rgba(16, 185, 129, 0.2); color: #047857; border: 1px solid rgba(16, 185, 129, 0.35); font-weight: 700; }
            .pill-info { background: rgba(14, 165, 233, 0.15); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.3); }
            .pill-purple { background: rgba(168, 85, 247, 0.15); color: #7e22ce; border: 1px solid rgba(168, 85, 247, 0.3); }
            .pill-gray { background: rgba(156, 163, 175, 0.15); color: #6b7280; border: 1px solid rgba(156, 163, 175, 0.3); }

            /* Modal Day Detail */
            .gcal-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
                z-index: 50;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            .gcal-modal-card {
                width: 100%;
                max-width: 520px;
                background: #ffffff;
                border-radius: 1rem;
                padding: 1.5rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                max-height: 90vh;
                overflow-y: auto;
            }
            .dark .gcal-modal-card {
                background: #1f2937;
                color: #f9fafb;
                border: 1px solid rgba(75, 85, 99, 0.5);
            }
            @media (max-width: 640px) {
                .gcal-cell {
                    min-height: 64px;
                    padding: 0.25rem;
                }
                .gcal-event-pill {
                    font-size: 0.6rem;
                    padding: 0.1rem 0.2rem;
                }
                .gcal-title {
                    font-size: 1.1rem;
                }
            }
        </style>

        <div class="gcal-container">
            
            <!-- HEADER NAVIGASI GOOGLE CALENDAR -->
            <div class="gcal-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 28px; height: 28px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <h2 class="gcal-title">{{ $monthTitle }}</h2>
                    </div>

                    <div class="gcal-nav-group">
                        <button type="button" wire:click="prevMonth" class="gcal-btn" title="Bulan Sebelumnya">
                            ‹
                        </button>
                        <button type="button" wire:click="goToToday" class="gcal-btn" title="Kembali ke Hari Ini">
                            Hari Ini
                        </button>
                        <button type="button" wire:click="nextMonth" class="gcal-btn" title="Bulan Berikutnya">
                            ›
                        </button>
                    </div>
                </div>

                <!-- LEGENDA WARNA STATUS -->
                <div class="gcal-legend">
                    <span class="gcal-legend-item pill-amber">🕒 Shift Kerja</span>
                    <span class="gcal-legend-item pill-danger">🔴 Toko Tutup</span>
                    <span class="gcal-legend-item pill-emerald">🏖️ Libur Rutin</span>
                    <span class="gcal-legend-item pill-info">🌴 Cuti/Izin</span>
                    <span class="gcal-legend-item pill-purple">⚡ Lembur</span>
                    <span class="gcal-legend-item pill-success">✅ Hadir</span>
                </div>
            </div>

            <!-- GRID KALENDER 7 KOLOM (SENIN - MINGGU) -->
            <div class="gcal-grid">
                
                <!-- HEADER HARI -->
                <div class="gcal-weekday">Senin</div>
                <div class="gcal-weekday">Selasa</div>
                <div class="gcal-weekday">Rabu</div>
                <div class="gcal-weekday">Kamis</div>
                <div class="gcal-weekday">Jumat</div>
                <div class="gcal-weekday">Sabtu</div>
                <div class="gcal-weekday" style="color: #ef4444;">Minggu</div>

                <!-- CELLS TANGGAL -->
                @foreach($days as $day)
                    <div 
                        wire:click="selectDate('{{ $day['date'] }}')"
                        class="gcal-cell {{ !$day['isCurrentMonth'] ? 'gcal-cell-dimmed' : '' }} {{ $day['isBeforeRegistered'] ? 'gcal-cell-pre-register' : '' }}"
                        title="{{ $day['isBeforeRegistered'] ? 'Belum terdaftar sebagai karyawan pada tanggal ini' : 'Klik untuk detail: ' . $day['date'] }}"
                    >
                        <div class="gcal-cell-header">
                            <span class="gcal-day-num {{ $day['isToday'] ? 'gcal-today-pill' : '' }}">
                                {{ $day['dayNumber'] }}
                            </span>
                            @if($day['isToday'])
                                <span style="font-size: 0.625rem; font-weight: 800; color: #f59e0b; text-transform: uppercase;">Hari Ini</span>
                            @elseif($day['isBeforeRegistered'])
                                <span style="font-size: 0.625rem; font-weight: 600; opacity: 0.5;">-</span>
                            @endif
                        </div>

                        <!-- EVENT CHIPS -->
                        @foreach($day['events'] as $evt)
                            <div class="gcal-event-pill pill-{{ $evt['type'] }}" title="{{ $evt['tooltip'] }}">
                                <span>{{ $evt['icon'] }}</span>
                                <span style="overflow: hidden; text-overflow: ellipsis;">{{ $evt['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>

        </div>

        <!-- MODAL DETAIL HARI (POPUP INTERAKTIF SAAT TANGGAL DIKLIK) -->
        @if($selectedDateDetail)
            <div class="gcal-modal-backdrop" wire:click.self="closeDateDetail">
                <div class="gcal-modal-card">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                        <div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.05em;">Detail Kalender Kerja</span>
                            <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0.25rem 0 0 0;">
                                {{ $selectedDateDetail['formattedDate'] }}
                            </h3>
                        </div>
                        <button type="button" wire:click="closeDateDetail" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: inherit; opacity: 0.6;">
                            ✕
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        
                        <!-- JIKA TANGGAL SEBELUM KARYAWAN TERDAFTAR -->
                        @if($selectedDateDetail['isBeforeRegistered'])
                            <div style="padding: 1.25rem; border-radius: 0.75rem; border: 1.5px dashed rgba(156, 163, 175, 0.4); background: rgba(156, 163, 175, 0.06); text-align: center;">
                                <div style="font-size: 1.75rem; margin-bottom: 0.35rem;">🗓️</div>
                                <strong style="font-size: 0.9375rem; display: block; color: inherit;">Belum Terdaftar sebagai Karyawan</strong>
                                <p style="font-size: 0.8125rem; opacity: 0.75; margin: 0.35rem 0 0 0;">
                                    Pada tanggal ini Anda belum terdaftar / belum mulai bekerja di kios.<br>
                                    Terhitung resmi mulai bekerja sejak tanggal: <strong>{{ $selectedDateDetail['tglMulaiFormatted'] }}</strong>.
                                </p>
                            </div>
                        @endif

                        <!-- 1. STATUS AGENDA / LIBUR TOKO (JIKA ADA DARI ADMIN) -->
                        @if($selectedDateDetail['agenda'])
                            <div style="padding: 1rem; border-radius: 0.75rem; background: {{ $selectedDateDetail['agenda']['isTokoTutup'] ? 'rgba(239, 68, 68, 0.1)' : 'rgba(245, 158, 11, 0.1)' }}; border: 1.5px solid {{ $selectedDateDetail['agenda']['isTokoTutup'] ? '#ef4444' : '#f59e0b' }};">
                                <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 800; font-size: 0.9375rem; color: {{ $selectedDateDetail['agenda']['isTokoTutup'] ? '#dc2626' : '#d97706' }};">
                                    <span>{{ $selectedDateDetail['agenda']['isTokoTutup'] ? '🔴 Toko Tutup (Libur Toko)' : '🧹 Agenda Toko' }}</span>
                                </div>
                                <div style="font-size: 0.875rem; font-weight: 700; margin-top: 0.25rem;">
                                    {{ $selectedDateDetail['agenda']['nama'] }}
                                </div>
                                @if($selectedDateDetail['agenda']['keterangan'])
                                    <div style="font-size: 0.8125rem; opacity: 0.85; margin-top: 0.25rem;">
                                        {{ $selectedDateDetail['agenda']['keterangan'] }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(!$selectedDateDetail['isBeforeRegistered'])
                            <!-- 2. STATUS JADWAL SHIFT & LIBUR RUTIN -->
                            <div style="padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.2); background: rgba(156, 163, 175, 0.05);">
                                <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.65; text-transform: uppercase;">Jadwal Shift Kerja</div>
                                @if($selectedDateDetail['isLiburRutin'])
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                                        <span style="font-size: 1.25rem;">🏖️</span>
                                        <div>
                                            <strong style="color: #10b981; font-size: 0.9375rem;">Hari Libur Rutin Mingguan</strong>
                                            <div style="font-size: 0.75rem; opacity: 0.75;">Jadwal libur tetap Anda setiap hari {{ $selectedDateDetail['hariLiburRutin'] }}.</div>
                                        </div>
                                    </div>
                                @elseif($selectedDateDetail['shift'])
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem;">
                                        <div>
                                            <strong style="font-size: 1rem;">{{ $selectedDateDetail['shift']['nama'] }}</strong>
                                            <div style="font-size: 0.8125rem; opacity: 0.75;">Jam Kerja: {{ $selectedDateDetail['shift']['jam_masuk'] }} - {{ $selectedDateDetail['shift']['jam_pulang'] }} WIB</div>
                                        </div>
                                        <span class="gcal-legend-item pill-amber">Shift Aktif</span>
                                    </div>
                                @else
                                    <div style="font-size: 0.875rem; opacity: 0.75; margin-top: 0.25rem;">Belum ada jadwal shift yang ditetapkan.</div>
                                @endif
                            </div>

                            <!-- 3. RIWAYAT PRESENSI KEHADIRAN (JIKA ADA) -->
                            @if($selectedDateDetail['absensi'])
                                <div style="padding: 1rem; border-radius: 0.75rem; border: 1.5px solid rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.08);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 0.75rem; font-weight: 700; color: #059669; text-transform: uppercase;">Catatan Presensi Riil</span>
                                        <span class="gcal-legend-item {{ $selectedDateDetail['absensi']['status_masuk'] === 'Terlambat' ? 'pill-warning' : 'pill-success' }}">{{ $selectedDateDetail['absensi']['status_masuk'] }}</span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 0.75rem;">
                                        <div>
                                            <span style="font-size: 0.75rem; opacity: 0.75;">Jam Masuk:</span>
                                            <strong style="display: block; font-size: 0.9375rem;">{{ $selectedDateDetail['absensi']['jam_masuk'] ?? '-' }} WIB</strong>
                                        </div>
                                        <div>
                                            <span style="font-size: 0.75rem; opacity: 0.75;">Jam Pulang:</span>
                                            <strong style="display: block; font-size: 0.9375rem;">{{ $selectedDateDetail['absensi']['jam_pulang'] ?? '-' }} WIB</strong>
                                        </div>
                                    </div>

                                    @if($selectedDateDetail['absensi']['menit_terlambat'] > 0)
                                        <div style="font-size: 0.75rem; color: #d97706; margin-top: 0.5rem; font-weight: 700;">
                                            ⚠️ Terlambat: {{ $selectedDateDetail['absensi']['menit_terlambat'] }} Menit
                                        </div>
                                    @endif
                                </div>
                            @elseif($selectedDateDetail['isPast'] && !$selectedDateDetail['isLiburRutin'] && (!$selectedDateDetail['agenda'] || !$selectedDateDetail['agenda']['isTokoTutup']) && !$selectedDateDetail['izin'])
                                <div style="padding: 0.875rem; border-radius: 0.75rem; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3); color: #dc2626; font-size: 0.8125rem;">
                                    ❌ <strong>Tidak Ada Presensi (Alpha):</strong> Anda tercatat tidak melakukan absensi pada hari kerja ini.
                                </div>
                            @endif

                            <!-- 4. STATUS IZIN / CUTI (JIKA ADA) -->
                            @if($selectedDateDetail['izin'])
                                <div style="padding: 1rem; border-radius: 0.75rem; background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.3);">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: #0284c7; text-transform: uppercase;">Pengajuan Izin / Cuti (Disetujui)</span>
                                    <div style="font-size: 0.9375rem; font-weight: 800; margin-top: 0.25rem;">{{ $selectedDateDetail['izin']['jenis'] }}</div>
                                    <div style="font-size: 0.8125rem; opacity: 0.85; margin-top: 0.25rem;">Alasan: {{ $selectedDateDetail['izin']['alasan'] }}</div>
                                </div>
                            @endif

                            <!-- 5. STATUS LEMBUR (JIKA ADA) -->
                            @if($selectedDateDetail['lembur'])
                                <div style="padding: 1rem; border-radius: 0.75rem; background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.3);">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: #7e22ce; text-transform: uppercase;">Pekerjaan Lembur (Disetujui)</span>
                                    <div style="font-size: 0.9375rem; font-weight: 800; margin-top: 0.25rem;">Durasi: {{ $selectedDateDetail['lembur']['durasi'] }} Jam ({{ $selectedDateDetail['lembur']['jam_mulai'] }} - {{ $selectedDateDetail['lembur']['jam_selesai'] }} WIB)</div>
                                    <div style="font-size: 0.8125rem; opacity: 0.85; margin-top: 0.25rem;">Uraian: {{ $selectedDateDetail['lembur']['tugas'] }}</div>
                                </div>
                            @endif
                        @endif

                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(156, 163, 175, 0.2);">
                        <button type="button" wire:click="closeDateDetail" class="gcal-btn">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
