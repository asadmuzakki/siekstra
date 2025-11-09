<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas_Ekskul extends Model
{
    protected $table = 'kelas_ekskuls';

    protected $fillable = [
        'ekskul_id',
        'nama_kelas',
        'tahun_ajaran',
        'status',
        'periode'
    ];

    public function ekskul()
    {
        return $this->belongsTo(Ekskul::class);
    }
    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }
}
