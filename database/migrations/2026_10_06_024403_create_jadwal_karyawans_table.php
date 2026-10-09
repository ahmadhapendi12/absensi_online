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
      Schema::create('jadwal_karyawans', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignUuid('shift_id')->constrained('master_shifts')->cascadeOnDelete();
        $table->date('tanggal_kerja');
        $table->timestamps();

        $table->unique(['user_id', 'tanggal_kerja']);
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_karyawans');
    }
};
