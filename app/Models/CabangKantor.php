<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CabangKantor extends Model
{
    use HasUuids;

    protected $fillable = [
        'nama_cabang', 'latitude', 'longitude', 'radius_meter', 'is_24_jam'
    ];

    protected $casts = [
        'is_24_jam' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function shifts()
    {
        return $this->hasMany(MasterShift::class, 'cabang_id');
    }
}