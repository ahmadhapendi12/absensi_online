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
        'foto_ktp',
        'foto_wajah_acuan',
        'berkas_identitas',
        'telphone',
        'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
