<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasEkskul extends Model
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
    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
