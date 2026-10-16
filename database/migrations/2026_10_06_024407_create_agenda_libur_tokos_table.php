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
        Schema::create('agenda_libur_tokos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cabang_id')->nullable()->constrained('cabang_kantors')->nullOnDelete();
            $table->string('nama_agenda');
            $table->date('tanggal');
            $table->string('jenis')->default('libur_kios'); // libur_kios, jumsih, kamsih, lainnya
            $table->string('status')->default('pending'); // pending, berlangsung, selesai, dibatalkan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_libur_tokos');
    }
};
