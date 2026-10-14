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

        $response->assertRedirect(route('karyawan.presensi.create'));
    }

    public function test_karyawan_cannot_access_filament_admin(): void
    {
        $karyawan = User::whereHas('roles', fn ($q) => $q->where('name', 'karyawan'))->first();
        $this->actingAs($karyawan);

        $response = $this->get('/admin');
        $response->assertStatus(403);
    }
}
