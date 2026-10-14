<?php

namespace App\Filament\Resources\CabangKantors\Pages;

use App\Filament\Resources\CabangKantors\CabangKantorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCabangKantor extends EditRecord
{
    protected static string $resource = CabangKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
