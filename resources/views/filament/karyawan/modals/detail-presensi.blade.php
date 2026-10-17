<div class="space-y-4">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; padding: 1rem; border-radius: 0.75rem; background: rgba(156, 163, 175, 0.08); border: 1px solid rgba(156, 163, 175, 0.2);">
        <div>
            <span style="font-size: 0.75rem; opacity: 0.65; display: block;">Tanggal Presensi</span>
            <strong style="font-size: 0.875rem;">{{ \Carbon\Carbon::parse($record->tanggal)->translatedFormat('l, d F Y') }}</strong>
        </div>
        <div>
            <span style="font-size: 0.75rem; opacity: 0.65; display: block;">Shift Kerja</span>
            <strong style="font-size: 0.875rem;">{{ $record->jadwal?->shift?->nama_shift ?? 'Reguler' }}</strong>
        </div>
        <div>
            <span style="font-size: 0.75rem; opacity: 0.65; display: block;">Status Kehadiran</span>
            <span style="display: inline-block; padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 700; background: {{ $record->status_masuk === 'Hadir' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }}; color: {{ $record->status_masuk === 'Hadir' ? '#059669' : '#d97706' }};">
                {{ $record->status_masuk }} @if($record->menit_terlambat > 0) (+{{ $record->menit_terlambat }} mnt) @endif
            </span>
        </div>
        <div>
            <span style="font-size: 0.75rem; opacity: 0.65; display: block;">Waktu Jam Kerja</span>
            <strong style="font-size: 0.875rem;">
                {{ $record->jam_masuk ? \Carbon\Carbon::parse($record->jam_masuk)->format('H:i') : '-' }} - {{ $record->jam_pulang ? \Carbon\Carbon::parse($record->jam_pulang)->format('H:i') . ' WIB' : 'Belum Pulang' }}
            </strong>
        </div>
    </div>

    <!-- Foto Masuk & Pulang -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1rem;">
        <!-- Foto Masuk -->
        <div style="text-align: center; padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.2); background: rgba(255, 255, 255, 0.5);">
            <strong style="display: block; font-size: 0.8125rem; margin-bottom: 0.5rem; color: #059669;">📸 Foto Absen Masuk</strong>
            @if($record->foto_masuk && file_exists(public_path('storage/' . $record->foto_masuk)))
                <img 
                    src="{{ asset('storage/' . $record->foto_masuk) }}" 
                    alt="Foto Masuk" 
                    style="width: 100%; max-height: 240px; object-fit: cover; border-radius: 0.5rem; border: 1px solid rgba(16, 185, 129, 0.4);"
                >
                <span style="font-size: 0.75rem; opacity: 0.75; display: block; margin-top: 0.35rem;">
                    Pukul {{ \Carbon\Carbon::parse($record->jam_masuk)->format('H:i:s') }} WIB
                </span>
            @else
                <div style="height: 140px; display: flex; align-items: center; justify-content: center; background: rgba(156, 163, 175, 0.1); border-radius: 0.5rem; font-size: 0.8125rem; opacity: 0.6;">
                    Foto tidak tersedia / sudah diarsipkan
                </div>
            @endif
        </div>

        <!-- Foto Pulang -->
        <div style="text-align: center; padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.2); background: rgba(255, 255, 255, 0.5);">
            <strong style="display: block; font-size: 0.8125rem; margin-bottom: 0.5rem; color: #4338ca;">📸 Foto Absen Pulang</strong>
            @if($record->foto_pulang && file_exists(public_path('storage/' . $record->foto_pulang)))
                <img 
                    src="{{ asset('storage/' . $record->foto_pulang) }}" 
                    alt="Foto Pulang" 
                    style="width: 100%; max-height: 240px; object-fit: cover; border-radius: 0.5rem; border: 1px solid rgba(99, 102, 241, 0.4);"
                >
                <span style="font-size: 0.75rem; opacity: 0.75; display: block; margin-top: 0.35rem;">
                    Pukul {{ \Carbon\Carbon::parse($record->jam_pulang)->format('H:i:s') }} WIB
                </span>
            @else
                <div style="height: 140px; display: flex; align-items: center; justify-content: center; background: rgba(156, 163, 175, 0.1); border-radius: 0.5rem; font-size: 0.8125rem; opacity: 0.6;">
                    {{ $record->jam_pulang ? 'Foto tidak tersedia / sudah diarsipkan' : 'Belum melakukan absen pulang' }}
                </div>
            @endif
        </div>
    </div>
</div>
