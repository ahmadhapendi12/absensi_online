<?php

namespace App\Filament\Resources\JadwalKaryawans;

use App\Filament\Resources\JadwalKaryawans\Pages\CreateJadwalKaryawan;
use App\Filament\Resources\JadwalKaryawans\Pages\EditJadwalKaryawan;
use App\Filament\Resources\JadwalKaryawans\Pages\ListJadwalKaryawans;
use App\Filament\Resources\JadwalKaryawans\Schemas\JadwalKaryawanForm;
use App\Filament\Resources\JadwalKaryawans\Tables\JadwalKaryawansTable;
use App\Models\JadwalKaryawan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalKaryawanResource extends Resource
{
    protected static ?string $model = JadwalKaryawan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Karyawan';

    protected static ?string $navigationLabel = 'Jadwal Kerja Karyawan';

    protected static ?string $modelLabel = 'Jadwal Kerja';

    protected static ?string $pluralModelLabel = 'Jadwal Kerja Karyawan';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'tanggal_kerja';

    public static function form(Schema $schema): Schema
    {
        return JadwalKaryawanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalKaryawansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalKaryawans::route('/'),
            'create' => CreateJadwalKaryawan::route('/create'),
            'edit' => EditJadwalKaryawan::route('/{record}/edit'),
        ];
    }
}
