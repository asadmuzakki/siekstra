<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'ekskul_id',
        'tanggal',
    ];
    
    public function ekskul()
    {
        return $this->belongsTo(Ekskul::class);
    }
    public function details()
    {
        return $this->hasMany(DetailNilai::class);
    }
}
