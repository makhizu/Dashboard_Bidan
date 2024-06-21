<?php

namespace App\Exports;

use App\Models\Anak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;


class AnakExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Anak::select('nama_bayi', 'nik', 'tanggal_lahir', 'pb_lahir', 'bb_lahir', 'tempat_lahir', 'jenis_kelamin')->get();
    }

    public function headings(): array{
        return ["Nama_Bayi", "NIK", "Tanggal_Lahir", "PB_Lahir", "BB_Lahir", "Tempat_Lahir", "Jenis_Kelamin"];
    }

    public function map($anak): array
    {
        // Set the 'nik' value as a string to prevent formatting
        return [
            $anak->nama_bayi,
            (string) $anak->nik,
            $anak->tanggal_lahir,
            $anak->pb_lahir,
            $anak->bb_lahir,
            $anak->tempat_lahir,
            $anak->jenis_kelamin,
        ];
    }
}
