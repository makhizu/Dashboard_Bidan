<?php

namespace App\Models;

use App\Http\Controllers\IbuController;
use App\Models\Ibu;
use App\Http\Controllers\KBController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluargaBerencana extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ibu()
    {
        // return $this->belongsToMany(Ibu::class, 'kb_ibu', 'nik', 'nik');
        return $this->hasMany(Ibu::class, 'nik', 'nik');
    }

}
