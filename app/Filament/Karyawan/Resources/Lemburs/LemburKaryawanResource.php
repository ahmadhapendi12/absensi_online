<?php

namespace App\Filament\Karyawan\Resources\Lemburs;

use App\Filament\Karyawan\Resources\Lemburs\Pages\CreateLemburKaryawan;
use App\Filament\Karyawan\Resources\Lemburs\Pages\ListLemburKaryawans;
use App\Filament\Karyawan\Resources\Lemburs\Schemas\LemburKaryawanForm;
use App\Filament\Karyawan\Resources\Lemburs\Tables\LemburKaryawansTable;
use App\Models\Lembur;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LemburKaryawanResource extends Resource
{
    protected static ?string $model = Lembur::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static UnitEnum|string|null $navigationGroup = 'Pengajuan';

    protected static ?string $navigationLabel = 'Pengajuan Lembur';

    protected static ?string $modelLabel = 'Pengajuan Lembur';

    protected static ?string $pluralModelLabel = 'Pengajuan Lembur';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return LemburKaryawanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LemburKaryawansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLemburKaryawans::route('/'),
            'create' => CreateLemburKaryawan::route('/create'),
        ];
    }
}
