<?php

namespace App\Filament\Karyawan\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;

class KalenderKerja extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static UnitEnum|string|null $navigationGroup = 'Menu Utama';

    protected static ?string $navigationLabel = 'Kalender Kerja';

    protected static ?string $title = 'Kalender Shift & Agenda Kerja';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.karyawan.pages.kalender-kerja';

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Karyawan\Widgets\KaryawanKalenderKerjaWidget::class,
        ];
    }
}
