<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Absensi;
use App\Models\JadwalKaryawan;
use App\Models\MasterShift;
use App\Services\BiometricFaceVerificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BiometricFaceVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_euclidean_distance_calculation(): void
    {
        $service = new BiometricFaceVerificationService();

        // Jarak antara 2 vektor identik adalah 0.0
        $vecA = array_fill(0, 128, 0.5);
        $vecB = array_fill(0, 128, 0.5);
        $distance = $service->calculateEuclideanDistance($vecA, $vecB);
        $this->assertEquals(0.0, $distance);

        // Vektor berbeda
        $vecC = array_fill(0, 128, 0.6);
        $diff = $service->calculateEuclideanDistance($vecA, $vecC);
        $this->assertGreaterThan(0.0, $diff);
    }

    public function test_fails_when_user_has_no_master_face(): void
    {
        $service = new BiometricFaceVerificationService();
        $user = User::factory()->create();
        UserProfile::create([
            'user_id' => $user->id,
            'foto_wajah_acuan' => null,
        ]);

        $fakePhoto = 'data:image/jpeg;base64,' . base64_encode('fake-image-bytes');

        $this->expectException(ValidationException::class);
        $service->prosesPresensi($user, $fakePhoto, '-6.2088,106.8456');
    }

    public function test_fails_when_face_descriptor_does_not_match(): void
    {
        $service = new BiometricFaceVerificationService();
        $user = User::factory()->create();

        // Vektor master: semua bernilai 0.1
        $masterVec = array_fill(0, 128, 0.1);
        UserProfile::create([
            'user_id' => $user->id,
            'foto_wajah_acuan' => 'wajah_acuan/master.jpg',
            'face_descriptor' => json_encode($masterVec),
        ]);

        // Vektor orang lain: semua bernilai 0.9 (sangat jauh berbeda)
        $imposterVec = array_fill(0, 128, 0.9);
        $fakePhoto = 'data:image/jpeg;base64,' . base64_encode('fake-image-bytes');

        $this->expectException(ValidationException::class);
        $service->prosesPresensi($user, $fakePhoto, '-6.2088,106.8456', json_encode($imposterVec));
    }

    public function test_successful_presensi_masuk_and_pulang_with_matching_biometrics(): void
    {
        $service = new BiometricFaceVerificationService();
        $user = User::factory()->create();

        $vector = array_fill(0, 128, 0.35);
        UserProfile::create([
            'user_id' => $user->id,
            'foto_wajah_acuan' => 'wajah_acuan/master.jpg',
            'face_descriptor' => json_encode($vector),
        ]);

        $fakePhoto = 'data:image/jpeg;base64,' . base64_encode('fake-image-bytes');

        // 1. Absen Masuk
        $resultMasuk = $service->prosesPresensi($user, $fakePhoto, '-6.2088,106.8456', json_encode($vector));
        $this->assertEquals('success', $resultMasuk['status']);
        $this->assertEquals('masuk', $resultMasuk['action']);

        $absensi = Absensi::where('user_id', $user->id)->where('tanggal', Carbon::today()->toDateString())->first();
        $this->assertNotNull($absensi);
        $this->assertNotNull($absensi->foto_masuk);
        $this->assertNull($absensi->jam_pulang);

        // 2. Absen Pulang
        $resultPulang = $service->prosesPresensi($user, $fakePhoto, '-6.2088,106.8456', json_encode($vector));
        $this->assertEquals('success', $resultPulang['status']);
        $this->assertEquals('pulang', $resultPulang['action']);

        $absensi->refresh();
        $this->assertNotNull($absensi->jam_pulang);
        $this->assertNotNull($absensi->foto_pulang);
    }

    public function test_prunes_photos_older_than_30_days_without_deleting_work_history(): void
    {
        $user = User::factory()->create();

        // 1. Buat record presensi 40 hari lalu (lebih dari 1 bulan)
        $oldDate = Carbon::now()->subDays(40)->toDateString();
        $oldPhoto = 'absensi/old_photo.jpg';
        Storage::disk('public')->put($oldPhoto, 'old-image-content');

        $oldAbsensi = Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $oldDate,
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
            'status_masuk' => 'Hadir',
            'foto_masuk' => $oldPhoto,
        ]);

        // 2. Buat record presensi kemarin (masih baru, < 30 hari)
        $recentDate = Carbon::now()->subDays(5)->toDateString();
        $recentPhoto = 'absensi/recent_photo.jpg';
        Storage::disk('public')->put($recentPhoto, 'recent-image-content');

        $recentAbsensi = Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $recentDate,
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
            'status_masuk' => 'Hadir',
            'foto_masuk' => $recentPhoto,
        ]);

        // 3. Jalankan artisan command pembersihan 30 hari
        $this->artisan('absensi:clean-old-photos --days=30')
            ->assertSuccessful();

        // 4. Verifikasi: Foto lama dihapus dari storage & db, tapi riwayat absensi tetap ada!
        Storage::disk('public')->assertMissing($oldPhoto);
        $oldAbsensi->refresh();
        $this->assertNull($oldAbsensi->foto_masuk);
        $this->assertEquals('Hadir', $oldAbsensi->status_masuk); // Riwayat jam kerja tetap ada!

        // Foto baru tidak boleh terhapus
        Storage::disk('public')->assertExists($recentPhoto);
        $recentAbsensi->refresh();
        $this->assertNotNull($recentAbsensi->foto_masuk);
    }
}
