<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ibu;
use App\Models\ImunisasiHDR;

class Anak extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function ibu()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->belongsTo(Ibu::class, 'nik', 'nik');
    }

    public function imunisasi()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->hasMany(ImunisasiHDR::class,  'id_anak');
    }
}


