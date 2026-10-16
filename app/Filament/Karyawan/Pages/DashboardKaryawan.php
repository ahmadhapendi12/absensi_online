<?php

namespace App\Filament\Karyawan\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class DashboardKaryawan extends BaseDashboard
{
    protected static ?string $title = 'Beranda Karyawan';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public function getWidgets(): array
    {
        return [
            \App\Filament\Karyawan\Widgets\KaryawanInfoWidget::class,
            \App\Filament\Karyawan\Widgets\KaryawanStatistikWidget::class,
        ];
    }
}
