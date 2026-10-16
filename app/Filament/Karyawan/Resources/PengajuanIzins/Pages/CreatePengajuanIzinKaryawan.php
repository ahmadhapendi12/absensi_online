<?php

namespace App\Filament\Karyawan\Resources\PengajuanIzins\Pages;

use App\Filament\Karyawan\Resources\PengajuanIzins\PengajuanIzinKaryawanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePengajuanIzinKaryawan extends CreateRecord
{
    protected static string $resource = PengajuanIzinKaryawanResource::class;

    protected static ?string $title = 'Form Pengajuan Izin / Cuti';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'Pending';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
