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
            $table->uuid()->primary();
            $table->uuid('user_id')->unique();
            $table->integer('nik' )->nullable();
            $table->string('name', 100)->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_wajah_acuan')->nullable();
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
