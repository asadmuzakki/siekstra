<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'siswa_id',
        'ekskul_id',
        'nilai',
        'keterangan'
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
