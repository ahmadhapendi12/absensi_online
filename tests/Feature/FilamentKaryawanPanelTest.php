<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class FilamentKaryawanPanelTest extends TestCase
{
    protected function getKaryawan(): User
    {
        return User::where('email', 'karyawan@gmail.com')->first() 
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'karyawan'))->first();
    }

    public function test_karyawan_can_access_karyawan_dashboard(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        $response = $this->get('/karyawan');
        $response->assertStatus(200);
        $response->assertSee($karyawan->name);
        $response->assertSee('Beranda Karyawan');
        $response->assertSee('Presensi Online');
        $response->assertSee('Hadir Tepat Waktu');
    }

    public function test_karyawan_can_access_presensi_mandiri_page(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        $response = $this->get('/karyawan/presensi-mandiri');
        $response->assertStatus(200);
        $response->assertSee('Presensi Kamera & GPS');
        $response->assertSee('Radius Toleransi');
    }

    public function test_karyawan_can_access_pengajuan_izin_resource(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        $this->get('/karyawan/pengajuan-izins/pengajuan-izin-karyawans')->assertStatus(200);
        $this->get('/karyawan/pengajuan-izins/pengajuan-izin-karyawans/create')->assertStatus(200);
    }

    public function test_karyawan_can_access_pengajuan_lembur_resource(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        $this->get('/karyawan/lemburs/lembur-karyawans')->assertStatus(200);
        $this->get('/karyawan/lemburs/lembur-karyawans/create')->assertStatus(200);
    }

    public function test_karyawan_can_access_rekap_presensi_resource(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        $response = $this->get('/karyawan/rekap-presensis/rekap-presensi-karyawans');
        $response->assertStatus(200);
        $response->assertSee('Unduh Laporan PDF');
    }

    public function test_karyawan_can_access_profil_page(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        $response = $this->get('/karyawan/profil-karyawan');
        $response->assertStatus(200);
        $response->assertSee($karyawan->name);
        $response->assertSee('Perbarui Data Kontak');
    }

    public function test_karyawan_cannot_access_admin_panel(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        $response = $this->get('/admin');
        $response->assertStatus(403);
    }

    public function test_login_redirects_karyawan_to_karyawan_panel(): void
    {
        $karyawan = $this->getKaryawan();

        $response = $this->post('/login', [
            'login' => $karyawan->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/karyawan');
    }

    public function test_karyawan_can_enroll_master_face(): void
    {
        $karyawan = $this->getKaryawan();
        $this->actingAs($karyawan);

        // 1x1 transparent png base64
        $fakeBase64 = 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        \Livewire\Livewire::test(\App\Filament\Karyawan\Pages\ProfilKaryawan::class)
            ->call('simpanFotoWajahMaster', $fakeBase64)
            ->assertHasNoErrors();

        $karyawan->refresh();
        $this->assertNotNull($karyawan->profile?->foto_wajah_acuan);
    }

    public function test_clean_old_presensi_photos_command(): void
    {
        $this->artisan('absensi:clean-old-photos --days=60')
            ->assertExitCode(0);
    }
}
