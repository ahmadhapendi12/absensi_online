<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Pages;

use App\Filament\Karyawan\Resources\Lemburs\LemburKaryawanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLemburKaryawans extends ListRecords
{
    protected static string $resource = LemburKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Lembur Baru'),
        ];
    }
}
