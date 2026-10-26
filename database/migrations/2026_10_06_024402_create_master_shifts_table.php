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
        Schema::create('master_shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cabang_id')->nullable()->constrained('cabang_kantors')->nullOnDelete();
            $table->string('nama_shift', 50);
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->integer('buka_absen_menit')->default(30);
            $table->integer('toleransi_terlambat_menit')->default(15);
            $table->integer('toleransi_pulang_menit')->default(0);
            $table->integer('batas_akhir_pulang_menit')->default(120);
            $table->boolean('lintas_hari')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_shifts');
    }
};
