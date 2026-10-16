<?php

namespace App\Filament\Karyawan\Resources\PengajuanIzins;

use App\Filament\Karyawan\Resources\PengajuanIzins\Pages\CreatePengajuanIzinKaryawan;
use App\Filament\Karyawan\Resources\PengajuanIzins\Pages\ListPengajuanIzinKaryawans;
use App\Filament\Karyawan\Resources\PengajuanIzins\Schemas\PengajuanIzinKaryawanForm;
use App\Filament\Karyawan\Resources\PengajuanIzins\Tables\PengajuanIzinKaryawansTable;
use App\Models\PengajuanIzin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PengajuanIzinKaryawanResource extends Resource
{
    protected static ?string $model = PengajuanIzin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Pengajuan Izin';

    protected static ?string $modelLabel = 'Pengajuan Izin';

    protected static ?string $pluralModelLabel = 'Pengajuan Izin / Cuti';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return PengajuanIzinKaryawanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengajuanIzinKaryawansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengajuanIzinKaryawans::route('/'),
            'create' => CreatePengajuanIzinKaryawan::route('/create'),
        ];
    }
}
