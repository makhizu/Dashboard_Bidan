<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ImunisasiDTL;
use App\Models\Ibu;
use App\Models\Anak;

class ImunisasiHDR extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function detail()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->hasMany(ImunisasiDTL::class, 'id_header', 'id');
    }

    public function ibu()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->hasMany(Ibu::class, 'nik', 'nik');
    }

    public function anak()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->belongsTo(Anak::class, 'id_anak');
    }
}
