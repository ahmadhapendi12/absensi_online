<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class FilamentAdminTest extends TestCase
{
    protected function getSuperadmin(): User
    {
        return User::where('email', 'superadmin@gmail.com')->first();
    }

    public function test_superadmin_can_access_filament_dashboard(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('StatsOverview');
        $response->assertSee('AttendanceChart');
        $response->assertSee('AttendanceDistributionChart');
        $response->assertSee('LatestAbsensiWidget');

        \Livewire\Livewire::test(\App\Filament\Widgets\StatsOverview::class)
            ->assertSee('Total Karyawan Aktif');
    }

    public function test_superadmin_can_access_users_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_jadwal_karyawan_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/jadwal-karyawans');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_lemburs_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/lemburs');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_pengajuan_izins_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/pengajuan-izins');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_master_shifts_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/master-shifts');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_cabang_kantors_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/cabang-kantors');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_absensis_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/absensis');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_agenda_libur_toko_resource(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/admin/agenda-libur-tokos');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_create_pages(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $this->get('/admin/users/create')->assertStatus(200);
        $this->get('/admin/jadwal-karyawans/create')->assertStatus(200);
        $this->get('/admin/lemburs/create')->assertStatus(200);
        $this->get('/admin/pengajuan-izins/create')->assertStatus(200);
        $this->get('/admin/master-shifts/create')->assertStatus(200);
        $this->get('/admin/cabang-kantors/create')->assertStatus(200);
        $this->get('/admin/absensis/create')->assertStatus(200);
        $this->get('/admin/agenda-libur-tokos/create')->assertStatus(200);
    }

    public function test_superadmin_can_access_edit_pages(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        if ($record = \App\Models\User::first()) {
            $this->get('/admin/users/' . $record->id . '/edit')->assertStatus(200);
        }
        if ($record = \App\Models\JadwalKaryawan::first()) {
            $this->get('/admin/jadwal-karyawans/' . $record->id . '/edit')->assertStatus(200);
        }
        if ($record = \App\Models\Lembur::first()) {
            $this->get('/admin/lemburs/' . $record->id . '/edit')->assertStatus(200);
        }
        if ($record = \App\Models\PengajuanIzin::first()) {
            $this->get('/admin/pengajuan-izins/' . $record->id . '/edit')->assertStatus(200);
        }
        if ($record = \App\Models\MasterShift::first()) {
            $this->get('/admin/master-shifts/' . $record->id . '/edit')->assertStatus(200);
        }
        if ($record = \App\Models\CabangKantor::first()) {
            $this->get('/admin/cabang-kantors/' . $record->id . '/edit')->assertStatus(200);
        }
        if ($record = \App\Models\Absensi::first()) {
            $this->get('/admin/absensis/' . $record->id . '/edit')->assertStatus(200);
        }
        if ($record = \App\Models\AgendaLiburToko::first()) {
            $this->get('/admin/agenda-libur-tokos/' . $record->id . '/edit')->assertStatus(200);
        }
    }

    public function test_superadmin_login_redirects_to_admin(): void
    {
        $response = $this->post('/login', [
            'login' => 'superadmin@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_karyawan_login_via_nik_redirects_to_karyawan_presensi(): void
    {
        $response = $this->post('/login', [
            'login' => '12345678', // NIK Budi
            'password' => 'password123',
        ]);

        $response->assertRedirect('/karyawan');
    }

    public function test_karyawan_cannot_access_filament_admin(): void
    {
        $karyawan = User::whereHas('roles', fn ($q) => $q->where('name', 'karyawan'))->first();
        $this->actingAs($karyawan);

        $response = $this->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_login_url_redirects_to_unified_login(): void
    {
        $response = $this->get('/admin/login');
        $response->assertRedirect('/login');
    }

    public function test_superadmin_can_export_pdf_report(): void
    {
        $user = $this->getSuperadmin();
        $this->actingAs($user);

        $response = $this->get('/laporan/export-pdf?tipe_periode=bulan&bulan=' . now()->month . '&tahun=' . now()->year);
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_karyawan_can_export_own_pdf_report(): void
    {
        $karyawan = User::whereHas('roles', fn ($q) => $q->where('name', 'karyawan'))->first();
        $this->actingAs($karyawan);

        $response = $this->get('/laporan/export-pdf?tipe_periode=bulan&bulan=' . now()->month . '&tahun=' . now()->year);
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_absensi_format_shows_exact_time_and_ongoing_status(): void
    {
        $karyawan = User::whereHas('roles', fn ($q) => $q->where('name', 'karyawan'))->first();
        $absensi = \App\Models\Absensi::create([
            'user_id' => $karyawan->id,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => '06:36:00',
            'jam_pulang' => null,
            'status_masuk' => 'Hadir',
            'menit_terlambat' => 0,
        ]);

        $this->assertEquals('06:36 - ..', $absensi->format_jam_kerja);
        $this->assertEquals('Sedang Berlangsung', $absensi->status_display);

        // Setelah absen pulang
        $absensi->update(['jam_pulang' => '15:00:00']);
        $this->assertEquals('06:36 - 15:00', $absensi->format_jam_kerja);
        $this->assertEquals('Hadir', $absensi->status_display);

        $absensi->delete();
    }
}
