<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

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
        return $this->hasRole('superadmin');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile?->foto_wajah_acuan) {
            return asset('storage/' . $this->profile->foto_wajah_acuan);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=FFE5E5&color=C53030';
    }

    public function getNikAttribute(): ?string
    {
        return $this->profile?->nik;
    }

    // Relasi
    public function cabang()
    {
        return $this->belongsTo(CabangKantor::class, 'cabang_id');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function jadwal()
    {
        return $this->hasOne(JadwalKaryawan::class, 'user_id');
    }
}