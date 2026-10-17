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
        if ($panel->getId() === 'admin') {
            return $this->hasRole('superadmin');
        }

        if ($panel->getId() === 'karyawan') {
            return $this->hasRole(['karyawan', 'superadmin']) && $this->status === 'active';
        }

        return false;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile?->foto_wajah_acuan && file_exists(public_path('storage/' . $this->profile->foto_wajah_acuan))) {
            return asset('storage/' . $this->profile->foto_wajah_acuan);
        }

        $initials = collect(explode(' ', $this->name))
            ->filter()
            ->map(fn ($segment) => mb_substr($segment, 0, 1))
            ->take(2)
            ->join('');
        $initials = strtoupper($initials ?: 'U');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
            . '<rect width="100" height="100" rx="50" fill="#fef3c7"/>'
            . '<text x="50" y="55" font-family="sans-serif" font-size="38" font-weight="700" fill="#b45309" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($initials) . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
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