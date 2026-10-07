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
      Schema::create('cabang_kantors', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('nama_cabang', 100);
    $table->string('latitude', 50);
    $table->string('longitude', 50);
    $table->integer('radius_meter')->default(100);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabang_kantors');
    }
};
