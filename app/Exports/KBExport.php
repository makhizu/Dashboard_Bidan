<?php

namespace App\Exports;

use App\Models\KeluargaBerencana;
use Maatwebsite\Excel\Concerns\FromCollection;

class KBExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return KeluargaBerencana::all();
    }
}
