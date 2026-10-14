<?php

namespace App\Filament\Resources\CabangKantors;

use App\Filament\Resources\CabangKantors\Pages\CreateCabangKantor;
use App\Filament\Resources\CabangKantors\Pages\EditCabangKantor;
use App\Filament\Resources\CabangKantors\Pages\ListCabangKantors;
use App\Filament\Resources\CabangKantors\Schemas\CabangKantorForm;
use App\Filament\Resources\CabangKantors\Tables\CabangKantorsTable;
use App\Models\CabangKantor;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CabangKantorResource extends Resource
{
    protected static ?string $model = CabangKantor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan Kantor';

    protected static ?string $navigationLabel = 'Lokasi Cabang Kantor';

    protected static ?string $modelLabel = 'Cabang Kantor';

    protected static ?string $pluralModelLabel = 'Lokasi Cabang Kantor';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_cabang';

    public static function form(Schema $schema): Schema
    {
        return CabangKantorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CabangKantorsTable::configure($table);
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
            'index' => ListCabangKantors::route('/'),
            'create' => CreateCabangKantor::route('/create'),
            'edit' => EditCabangKantor::route('/{record}/edit'),
        ];
    }
}
