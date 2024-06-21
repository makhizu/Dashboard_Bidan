<?php

namespace App\Exports;

use App\Models\Ibu;
use Maatwebsite\Excel\Concerns\FromCollection;


class IbuExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Ibu::all();
    }
}
