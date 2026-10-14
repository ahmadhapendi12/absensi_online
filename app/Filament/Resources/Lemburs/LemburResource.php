<?php

namespace App\Filament\Resources\Lemburs;

use App\Filament\Resources\Lemburs\Pages\CreateLembur;
use App\Filament\Resources\Lemburs\Pages\EditLembur;
use App\Filament\Resources\Lemburs\Pages\ListLemburs;
use App\Filament\Resources\Lemburs\Schemas\LemburForm;
use App\Filament\Resources\Lemburs\Tables\LembursTable;
use App\Models\Lembur;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LemburResource extends Resource
{
    protected static ?string $model = Lembur::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static UnitEnum|string|null $navigationGroup = 'Persetujuan & Izin';

    protected static ?string $navigationLabel = 'Pengajuan Lembur';

    protected static ?string $modelLabel = 'Pengajuan Lembur';

    protected static ?string $pluralModelLabel = 'Pengajuan Lembur';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'tanggal';

    public static function form(Schema $schema): Schema
    {
        return LemburForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LembursTable::configure($table);
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
            'index' => ListLemburs::route('/'),
            'create' => CreateLembur::route('/create'),
            'edit' => EditLembur::route('/{record}/edit'),
        ];
    }
}
