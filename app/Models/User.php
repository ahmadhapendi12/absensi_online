<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles; // Tambahkan HasUuids di sini

    protected $fillable = [
        'name', 'email', 'email_verified_at', 'status', 'password', 'cabang_id'
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

     public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole('super_admin');
    }

    // Relasi
    public function cabang()
    {
        return $this->belongsTo(CabangKantor::class, 'cabang_id');
    }
}