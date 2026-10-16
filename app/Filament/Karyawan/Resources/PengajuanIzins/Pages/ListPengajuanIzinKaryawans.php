<?php

namespace App\Filament\Karyawan\Resources\PengajuanIzins\Pages;

use App\Filament\Karyawan\Resources\PengajuanIzins\PengajuanIzinKaryawanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanIzinKaryawans extends ListRecords
{
    protected static string $resource = PengajuanIzinKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Izin / Cuti'),
        ];
    }
}
