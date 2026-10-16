<?php

namespace App\Filament\Resources\AgendaLiburTokos;

use App\Filament\Resources\AgendaLiburTokos\Pages\CreateAgendaLiburToko;
use App\Filament\Resources\AgendaLiburTokos\Pages\EditAgendaLiburToko;
use App\Filament\Resources\AgendaLiburTokos\Pages\ListAgendaLiburTokos;
use App\Filament\Resources\AgendaLiburTokos\Schemas\AgendaLiburTokoForm;
use App\Filament\Resources\AgendaLiburTokos\Tables\AgendaLiburTokosTable;
use App\Models\AgendaLiburToko;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AgendaLiburTokoResource extends Resource
{
    protected static ?string $model = AgendaLiburToko::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static \UnitEnum|string|null $navigationGroup = 'Pengaturan Kantor';

    protected static ?string $navigationLabel = 'Agenda & Libur Kios';

    protected static ?string $modelLabel = 'Agenda / Libur Toko';

    protected static ?string $pluralModelLabel = 'Agenda & Libur Toko';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AgendaLiburTokoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgendaLiburTokosTable::configure($table);
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
            'index' => ListAgendaLiburTokos::route('/'),
            'create' => CreateAgendaLiburToko::route('/create'),
            'edit' => EditAgendaLiburToko::route('/{record}/edit'),
        ];
    }
}
