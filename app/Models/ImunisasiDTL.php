<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ImunisasiHDR;

class ImunisasiDTL extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function header()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->belongsTo(ImunisasiHDR::class, 'id_header', 'id');
    }
}
