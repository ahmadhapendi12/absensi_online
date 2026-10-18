<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserProfile extends Model
{

    use HasUuids;
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'nik',
        'name',
        'tanggal_bergabung',
        'foto_ktp',
        'foto_wajah_acuan',
        'wajah_terverifikasi_at',
        'face_descriptor',
        'is_biometric_locked',
        'catatan_biometrik',
        'berkas_identitas',
        'telphone',
        'alamat',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'is_biometric_locked' => 'boolean',
        'wajah_terverifikasi_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
