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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('alasan_lembur');
            $table->enum('status_pengajuan', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->foreignUuid('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->time('jam_mulai_aktual')->nullable();
            $table->time('jam_selesai_aktual')->nullable();
            $table->string('foto_mulai')->nullable();
            $table->string('foto_selesai')->nullable();
            $table->string('koordinat_mulai', 100)->nullable();
            $table->string('koordinat_selesai', 100)->nullable();
            $table->integer('durasi_menit')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};
