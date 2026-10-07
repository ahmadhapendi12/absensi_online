<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lembur extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'tanggal', 'alasan_lembur', 'status_pengajuan',
        'disetujui_oleh', 'jam_mulai_aktual', 'jam_selesai_aktual',
        'foto_mulai', 'foto_selesai', 'koordinat_mulai', 'koordinat_selesai',
        'durasi_menit'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}