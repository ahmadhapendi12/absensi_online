<?php

namespace App\Filament\Resources\MasterShifts\Pages;

use App\Filament\Resources\MasterShifts\MasterShiftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterShift extends EditRecord
{
    protected static string $resource = MasterShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
