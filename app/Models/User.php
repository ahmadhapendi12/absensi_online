<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Tambahkan ini

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids; // Tambahkan HasUuids di sini

    protected $fillable = [
        'cabang_id', 'nik', 'name', 'email', 'password', 'role', 
        'no_hp', 'alamat', 'foto_wajah_acuan', 'foto_ktp', 
        'berkas_identitas', 'sisa_cuti'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi
    public function cabang()
    {
        return $this->belongsTo(CabangKantor::class, 'cabang_id');
    }
}