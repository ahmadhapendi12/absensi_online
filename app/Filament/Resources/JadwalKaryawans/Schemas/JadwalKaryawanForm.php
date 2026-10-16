<?php

namespace App\Filament\Resources\JadwalKaryawans\Schemas;

use App\Models\JadwalKaryawan;
use Carbon\Carbon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class JadwalKaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('karyawan_info')
                    ->label('Karyawan Terpilih')
                    ->content(function (?JadwalKaryawan $record): HtmlString {
                        if (!$record || !$record->user) {
                            return new HtmlString('<span class="text-gray-400">-</span>');
                        }
                        $user = $record->user;
                        $nik = $user->profile?->nik ?? '-';
                        $cabang = $user->cabang?->nama_cabang ?? 'Pusat';
                        $statusBadge = $user->status === 'active'
                            ? '<span style="display:inline-block; padding:2px 8px; font-size:11px; font-weight:600; border-radius:9999px; background:#DEF7EC; color:#03543F;">Aktif</span>'
                            : '<span style="display:inline-block; padding:2px 8px; font-size:11px; font-weight:600; border-radius:9999px; background:#FDE8E8; color:#9B1C1C;">Nonaktif</span>';

                        return new HtmlString("
                            <div style='display:flex; align-items:center; gap:12px; padding:10px 14px; background:#F9FAFB; border-radius:12px; border:1px solid #E5E7EB;'>
                                <img src='{$user->avatar_url}' style='width:40px; height:40px; border-radius:9999px; object-fit:cover; border:1px solid #D1D5DB;'>
                                <div>
                                    <div style='font-weight:700; color:#111827; font-size:14px;'>{$user->name} {$statusBadge}</div>
                                    <div style='font-size:12px; color:#6B7280; margin-top:2px;'>NIK: {$nik} &bull; Cabang: {$cabang}</div>
                                </div>
                            </div>
                        ");
                    })
                    ->visibleOn('edit'),

                Select::make('user_id')
                    ->label('Pilih Karyawan')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->role('karyawan')
                            ->where('status', 'active')
                            ->whereNotIn('id', JadwalKaryawan::pluck('user_id'))
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} (NIK: " . ($record->profile?->nik ?? '-') . " - " . ($record->cabang?->nama_cabang ?? 'Pusat') . ")")
                    ->searchable(['name'])
                    ->preload()
                    ->unique(table: 'jadwal_karyawans', column: 'user_id', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Karyawan ini sudah memiliki jadwal shift! Silakan edit jadwal yang ada.',
                    ])
                    ->helperText('Hanya menampilkan karyawan aktif yang belum memiliki penetapan shift.')
                    ->required()
                    ->visibleOn('create'),

                Grid::make(2)
                    ->schema([
                        Select::make('shift_id')
                            ->label('Shift Kerja yang Ditetapkan')
                            ->relationship('shift', 'nama_shift')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_shift} (" . Carbon::parse($record->jam_masuk)->format('H:i') . ' - ' . Carbon::parse($record->jam_pulang)->format('H:i') . ' WIB)')
                            ->searchable()
                            ->preload()
                            ->helperText('Sistem otomatis menjadwalkan karyawan ini setiap hari kerja sesuai shift yang dipilih.')
                            ->required(),

                        Select::make('hari_libur_rutin')
                            ->label('Hari Libur Rutin Mingguan (Off)')
                            ->options([
                                'senin' => 'Senin',
                                'selasa' => 'Selasa',
                                'rabu' => 'Rabu',
                                'kamis' => 'Kamis',
                                'jumat' => 'Jumat',
                                'sabtu' => 'Sabtu',
                                'minggu' => 'Minggu',
                            ])
                            ->default('senin')
                            ->required()
                            ->helperText('Jatah libur 1 hari seminggu. Pada hari ini karyawan tidak wajib absen dan tidak dihitung alpa.'),
                    ]),
            ]);
    }
}
