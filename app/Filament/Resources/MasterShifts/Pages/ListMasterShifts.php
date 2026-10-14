<?php

namespace App\Filament\Resources\MasterShifts\Pages;

use App\Filament\Resources\MasterShifts\MasterShiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterShifts extends ListRecords
{
    protected static string $resource = MasterShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
