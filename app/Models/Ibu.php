<?php

namespace App\Models;

use App\Models\KeluargaBerencana;
use App\Http\Controllers\KBController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ibu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function KB()
    {
        // return $this->belongsToMany(KeluargaBerencana::class, 'kb_ibu', 'nik', 'nik');
        return $this->hasMany(KeluargaBerencana::class, 'nik', 'nik');
    }
}
