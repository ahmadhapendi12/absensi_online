<?php

namespace App\Filament\Resources\PengajuanIzins\Pages;

use App\Filament\Resources\PengajuanIzins\PengajuanIzinResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanIzin extends EditRecord
{
    protected static string $resource = PengajuanIzinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
