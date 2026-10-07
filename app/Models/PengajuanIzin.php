<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PengajuanIzin extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai',
        'alasan', 'file_bukti', 'status', 'disetujui_oleh'
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