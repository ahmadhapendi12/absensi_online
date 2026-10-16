<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AgendaLiburToko extends Model
{
    use HasUuids;

    protected $fillable = [
        'cabang_id',
        'nama_agenda',
        'tanggal',
        'jenis',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function cabang()
    {
        return $this->belongsTo(CabangKantor::class, 'cabang_id');
    }
}
