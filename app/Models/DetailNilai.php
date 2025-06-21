<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailNilai extends Model
{
    protected $fillable = [
        'nilai_id',
        'siswa_id',
        'kehadiran',
        'keaktifan',
        'praktik',
        'nilai_akhir',
        'index_nilai',
        'keterangan',
    ];

    public function nilai()
    {
        return $this->belongsTo(Nilai::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
