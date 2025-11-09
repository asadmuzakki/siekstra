<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $fillable = [
        'siswa_id',
        'kelas_ekskul_id',
        'tanggal_pendaftaran',
        'jumlah_pindah',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // public function ekskul()
    // {
    //     return $this->belongsTo(Ekskul::class);
    // }
    public function kelas_ekskul()
    {
        return $this->belongsTo(Kelas_Ekskul::class);
    }
}
