<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MasterShift;
use App\Models\JadwalKaryawan;
use App\Models\Absensi;
use App\Models\Lembur;
use App\Services\BiometricFaceVerificationService;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;

class ShiftAttendanceValidationTest extends TestCase
{
    protected BiometricFaceVerificationService $service;
    protected ReflectionMethod $simpanMethod;
    protected User $karyawan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BiometricFaceVerificationService::class);
        $this->simpanMethod = new ReflectionMethod($this->service, 'simpanKeEntityAbsensi');
        $this->simpanMethod->setAccessible(true);
        $this->karyawan = User::where('email', 'karyawan@gmail.com')->first() 
            ?? User::role('karyawan')->first();
    }

    public function test_shift_3_cross_day_blocks_check_in_during_daytime(): void
    {
        $shift3 = MasterShift::where('lintas_hari', true)->first();
        if (!$shift3) {
            $this->markTestSkipped('Shift 3 tidak ditemukan');
        }

        JadwalKaryawan::updateOrCreate(['user_id' => $this->karyawan->id], ['shift_id' => $shift3->id]);
        Absensi::where('user_id', $this->karyawan->id)->delete();

        // Siang hari (13:30) harus ditolak
        $this->expectException(ValidationException::class);
        $this->simpanMethod->invoke($this->service, $this->karyawan, '2026-10-26', '13:30:00', '-6.12,106.15', 'dummy.jpg');
    }

    public function test_shift_3_cross_day_blocks_premature_checkout(): void
    {
        $shift3 = MasterShift::where('lintas_hari', true)->first();
        if (!$shift3) {
            $this->markTestSkipped('Shift 3 tidak ditemukan');
        }

        JadwalKaryawan::updateOrCreate(['user_id' => $this->karyawan->id], ['shift_id' => $shift3->id]);
        Absensi::where('user_id', $this->karyawan->id)->delete();

        // Check in di waktu yang valid (22:15 malam)
        $this->simpanMethod->invoke($this->service, $this->karyawan, '2026-10-26', '22:15:00', '-6.12,106.15', 'dummy_in.jpg');

        // Coba checkout jam 23:30 malam (belum jam 07:00 pagi) -> harus ditolak
        $this->expectException(ValidationException::class);
        $this->simpanMethod->invoke($this->service, $this->karyawan, '2026-10-26', '23:30:00', '-6.12,106.15', 'dummy_out.jpg');
    }

    public function test_shift_1_overtime_calculation(): void
    {
        $shift1 = MasterShift::where('nama_shift', 'Shift 1')->where('jam_masuk', '07:00:00')->first();
        if (!$shift1) {
            $this->markTestSkipped('Shift 1 07:00 tidak ditemukan');
        }

        JadwalKaryawan::updateOrCreate(['user_id' => $this->karyawan->id], ['shift_id' => $shift1->id]);
        Absensi::where('user_id', $this->karyawan->id)->delete();
        Lembur::where('user_id', $this->karyawan->id)->delete();

        // Masuk di 05:45 (antara 05:30 s/d 06:00) -> Lembur otomatis 1 jam penuh (60m)
        $res = $this->simpanMethod->invoke($this->service, $this->karyawan, '2026-10-26', '05:45:00', '-6.12,106.15', 'dummy_s1.jpg');
        $this->assertEquals('Hadir', $res['status_masuk']);

        $lembur = Lembur::where('user_id', $this->karyawan->id)->first();
        $this->assertNotNull($lembur);
        $this->assertEquals(60, $lembur->durasi_menit);
    }

    public function test_shift_2_does_not_create_automatic_overtime(): void
    {
        $shift2 = MasterShift::where('nama_shift', 'Shift 2')->where('jam_masuk', '15:00:00')->first();
        if (!$shift2) {
            $this->markTestSkipped('Shift 2 15:00 tidak ditemukan');
        }

        JadwalKaryawan::updateOrCreate(['user_id' => $this->karyawan->id], ['shift_id' => $shift2->id]);
        Absensi::where('user_id', $this->karyawan->id)->delete();
        Lembur::where('user_id', $this->karyawan->id)->delete();

        // Masuk di 14:15 (antara buka absen 14:00 s/d 15:00) -> Hadir, TIDAK ADA lembur otomatis
        $res = $this->simpanMethod->invoke($this->service, $this->karyawan, '2026-10-26', '14:15:00', '-6.12,106.15', 'dummy_s2.jpg');
        $this->assertEquals('Hadir', $res['status_masuk']);

        $lembur = Lembur::where('user_id', $this->karyawan->id)->first();
        $this->assertNull($lembur);
    }

    public function test_employee_who_attempts_check_in_after_shift_hours_is_marked_as_libur(): void
    {
        $shift1 = MasterShift::where('nama_shift', 'Shift 1')->where('jam_masuk', '07:00:00')->first();
        JadwalKaryawan::updateOrCreate(['user_id' => $this->karyawan->id], ['shift_id' => $shift1->id]);
        Absensi::where('user_id', $this->karyawan->id)->delete();

        // Coba absen masuk jam 16:00 (setelah shift 07:00 - 15:00 berakhir) -> ditolak & dicatat Libur
        try {
            $this->simpanMethod->invoke($this->service, $this->karyawan, '2026-10-26', '16:00:00', '-6.12,106.15', 'dummy_s1.jpg');
            $this->fail('Harusnya melempar ValidationException');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('telah berakhir', $e->getMessage());
            $this->assertStringContainsString('Libur', $e->getMessage());
        }

        $record = Absensi::where('user_id', $this->karyawan->id)->where('tanggal', '2026-10-26')->first();
        $this->assertNotNull($record);
        $this->assertEquals('Libur', $record->status_masuk);
        $this->assertNull($record->jam_masuk);
    }

    public function test_auto_libur_service_marks_past_ended_shift_as_libur(): void
    {
        $shift1 = MasterShift::where('nama_shift', 'Shift 1')->first();
        JadwalKaryawan::updateOrCreate(['user_id' => $this->karyawan->id], ['shift_id' => $shift1->id]);
        
        $pastDate = '2026-10-20';
        Absensi::where('user_id', $this->karyawan->id)->where('tanggal', $pastDate)->delete();

        $autoLiburService = app(\App\Services\AbsensiAutoLiburService::class);
        $autoLiburService->syncLibur($pastDate, $this->karyawan);

        $record = Absensi::where('user_id', $this->karyawan->id)->where('tanggal', $pastDate)->first();
        $this->assertNotNull($record);
        $this->assertEquals('Libur', $record->status_masuk);
        $this->assertNull($record->jam_masuk);
    }
}

