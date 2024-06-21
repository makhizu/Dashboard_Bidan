<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ibu;

class Kehamilan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ibu()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->hasMany(Ibu::class, 'nik', 'nik');
    }
}
