<?php

namespace App\Filament\Karyawan\Resources\RekapPresensis;

use App\Filament\Karyawan\Resources\RekapPresensis\Pages\ListRekapPresensiKaryawans;
use App\Filament\Karyawan\Resources\RekapPresensis\Tables\RekapPresensiKaryawansTable;
use App\Models\Absensi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RekapPresensiKaryawanResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Rekap Presensi Saya';

    protected static ?string $modelLabel = 'Rekap Presensi';

    protected static ?string $pluralModelLabel = 'Rekap Presensi Saya';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['jadwal.shift'])
            ->where('user_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return RekapPresensiKaryawansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRekapPresensiKaryawans::route('/'),
        ];
    }
}
