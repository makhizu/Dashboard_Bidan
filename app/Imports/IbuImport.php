<?php

namespace App\Imports;

use App\Models\Ibu;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class IbuImport implements ToModel, WithHeadingRow, WithValidation
{

    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $countData = [];
    protected $duplicateRow = 0;
    protected $successInsert = 0;
    protected $nikNull = 0;
    protected $allData = 0;
    

    public function model(array $row)
    {           
        $this->allData++;
        $nik = $row['nik'];

        if (!$nik || strlen($nik) != 16 ) {
            $this->nikNull++;
            return null;
        }

        $suami = $row['suami'];
        if (!$suami) {
            $row['suami'] = 'NONE';
        }

        // $ibu = Ibu::where('nik', $row['nik'])->first();

        // if () {
        //     # code...
        // }

        $existingRow = Ibu::where('nik', $row['nik'])->exists();
        // ->where('name')

        if($existingRow) {
            $this->duplicateRow++;

            $this->countData[$row['nik'] . '|' . $row['nama'] . '|' . $row['suami'] . '|' . $row['alamat']] = ($this->countData[$row['nik'] . '|' . $row['nama'] . '|' . $row['suami'] . '|' . $row['alamat']] ?? 0) + 1;
            return null;
        }

        $this->successInsert++;
        return new Ibu([
            'nama' => $row['nama'],
            'nik' => $row['nik'],
            'suami' => $row['suami'],
            'alamat' => $row['alamat'],
        ]);

    }

    public function rules(): array
    {
        return [
            // 'nama' => 'required',
            // 'nik' => 'numeric',      
        ];
    }
    

    public function getCountData()
    {
        return $this->countData;
    }

    public function getDuplicateRow()
    {
        return $this->duplicateRow;
    }

    public function getSuccessInsert()
    {
        return $this->successInsert;
    }

    public function getNIKnull()
    {
        return $this->nikNull;
    }

    public function getAllData()
    {
        return $this->allData;
    }

    
}
