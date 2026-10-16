<?php

namespace App\Filament\Resources\AgendaLiburTokos\Pages;

use App\Filament\Resources\AgendaLiburTokos\AgendaLiburTokoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAgendaLiburToko extends EditRecord
{
    protected static string $resource = AgendaLiburTokoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
