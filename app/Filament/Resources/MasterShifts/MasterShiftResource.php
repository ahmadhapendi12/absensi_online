<?php

namespace App\Filament\Resources\MasterShifts;

use App\Filament\Resources\MasterShifts\Pages\CreateMasterShift;
use App\Filament\Resources\MasterShifts\Pages\EditMasterShift;
use App\Filament\Resources\MasterShifts\Pages\ListMasterShifts;
use App\Filament\Resources\MasterShifts\Schemas\MasterShiftForm;
use App\Filament\Resources\MasterShifts\Tables\MasterShiftsTable;
use App\Models\MasterShift;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterShiftResource extends Resource
{
    protected static ?string $model = MasterShift::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan Kantor';

    protected static ?string $navigationLabel = 'Jam Kerja & Shift';

    protected static ?string $modelLabel = 'Jam Kerja / Shift';

    protected static ?string $pluralModelLabel = 'Jam Kerja & Shift';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nama_shift';

    public static function form(Schema $schema): Schema
    {
        return MasterShiftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterShiftsTable::configure($table);
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
            'index' => ListMasterShifts::route('/'),
            'create' => CreateMasterShift::route('/create'),
            'edit' => EditMasterShift::route('/{record}/edit'),
        ];
    }
}
