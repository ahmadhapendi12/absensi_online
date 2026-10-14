<?php

namespace App\Filament\Resources\CabangKantors\Pages;

use App\Filament\Resources\CabangKantors\CabangKantorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCabangKantors extends ListRecords
{
    protected static string $resource = CabangKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
