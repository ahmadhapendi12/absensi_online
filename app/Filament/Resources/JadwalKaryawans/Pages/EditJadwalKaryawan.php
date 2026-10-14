<?php

namespace App\Filament\Resources\JadwalKaryawans\Pages;

use App\Filament\Resources\JadwalKaryawans\JadwalKaryawanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJadwalKaryawan extends EditRecord
{
    protected static string $resource = JadwalKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
