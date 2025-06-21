<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'siswa_id',
        'ekskul_id',
        'keterangan',
        'kehadiran',
        'keaktifan',
        'praktik',
        'nilai_akhir',
        'index_nilai',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    public function ekskul()
    {
        return $this->belongsTo(Ekskul::class);
    }
}
