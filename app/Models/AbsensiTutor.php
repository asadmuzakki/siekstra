<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiTutor extends Model
{
    protected $fillable = [
        'tutor_id',
        'kelas_ekskul_id',
        'tanggal',
        'keterangan',
        'status'
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
    // public function ekskul()
    // {
    //     return $this->belongsTo(Ekskul::class);
    // }
    public function kelas_ekskul()
    {
        return $this->belongsTo(KelasEkskul::class, 'kelas_ekskul_id');
    }
}
