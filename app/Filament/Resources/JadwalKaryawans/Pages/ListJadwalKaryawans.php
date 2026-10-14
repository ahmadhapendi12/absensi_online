<?php

namespace App\Filament\Resources\JadwalKaryawans\Pages;

use App\Filament\Resources\JadwalKaryawans\JadwalKaryawanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalKaryawans extends ListRecords
{
    protected static string $resource = JadwalKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
