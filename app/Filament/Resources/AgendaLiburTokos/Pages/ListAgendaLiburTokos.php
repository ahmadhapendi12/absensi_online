<?php

namespace App\Filament\Resources\AgendaLiburTokos\Pages;

use App\Filament\Resources\AgendaLiburTokos\AgendaLiburTokoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAgendaLiburTokos extends ListRecords
{
    protected static string $resource = AgendaLiburTokoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
