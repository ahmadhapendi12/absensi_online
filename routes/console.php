<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal pembersihan otomatis berkas fisik foto presensi > 30 hari (1 bulan) setiap hari
Schedule::command('absensi:clean-old-photos --days=30')->daily();
