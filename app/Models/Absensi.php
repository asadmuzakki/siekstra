<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
   protected $fillable = [
      'kelas_ekskul_id',
      'agenda',
      'tanggal'
   ];

   public function details()
   {
      return $this->hasMany(DetailAbsensi::class);
   }

   // public function ekskul()
   // {
   //    return $this->belongsTo(Ekskul::class);
   // }
   public function kelas_ekskul()
   {
      return $this->belongsTo(KelasEkskul::class, 'kelas_ekskul_id');
   }
}
