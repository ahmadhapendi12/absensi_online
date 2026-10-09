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
        Schema::create('absensis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('jadwal_id')->nullable()->constrained('jadwal_karyawans')->nullOnDelete();
            $table->foreignUuid('pengajuan_izin_id')->nullable()->constrained('pengajuan_izins')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->string('koordinat_masuk', 100)->nullable();
            $table->string('koordinat_pulang', 100)->nullable();
            $table->enum('status_masuk', ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Cuti', 'Alpha'])->default('Alpha');
            $table->integer('menit_terlambat')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'tanggal']);
            $table->index(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
