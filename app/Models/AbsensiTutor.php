<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiTutor extends Model
{
    protected $fillable = [
        'tutor_id',
        'ekskul_id',
        'tanggal',
        'keterangan',
        'status'
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
    public function ekskul()
    {
        return $this->belongsTo(Ekskul::class);
    }
}
