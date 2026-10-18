<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('nik', 30)->nullable()->unique();
            $table->string('name', 100)->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_wajah_acuan')->nullable();
            $table->timestamp('wajah_terverifikasi_at')->nullable();
            $table->text('face_descriptor')->nullable();
            $table->boolean('is_biometric_locked')->default(false);
            $table->string('catatan_biometrik')->nullable();
            $table->string('berkas_identitas')->nullable();
            $table->string('telphone',100)->nullable();
            $table->string('alamat')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
