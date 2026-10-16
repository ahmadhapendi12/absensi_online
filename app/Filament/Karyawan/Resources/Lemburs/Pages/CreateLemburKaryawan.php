<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Pages;

use App\Filament\Karyawan\Resources\Lemburs\LemburKaryawanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLemburKaryawan extends CreateRecord
{
    protected static string $resource = LemburKaryawanResource::class;

    protected static ?string $title = 'Form Pengajuan Lembur';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['cabang_id'] = $user->cabang_id;
        $data['status_pengajuan'] = 'Pending';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
