<?php

namespace App\Imports;

use App\Models\KeluargaBerencana;
use App\Models\Ibu;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KBImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $noData = [];
    protected $dupData = [];
    protected $countNoData = 0;
    protected $countNoNik = 0;
    protected $successInsert = 0;
    protected $duplicateRow = 0;
    protected $allData = 0;


    public function model(array $row)
    {

        $nik = $row['nik'];

        if (!$nik || strlen($nik) != 16) {
            $this->countNoNik++;
            $this->allData++;
            return null;
        }

        $data = Ibu::where('nik', $nik)->get();

        if($data->isEmpty()) {
            $this->countNoData++;
            $this->allData++;

            $this->noData[$row['nik']] = ($this->noData[$row['nik']] ?? 0) + 1;

            return null;
        }

        $date = Date::excelToDateTimeObject($row['tanggal_kunjungan']);
        $formatDate = $date->format('Y-m-d');

        // $existingRow = [];
        $existingRow = KeluargaBerencana::where('tanggal_kunjungan', $formatDate)
                                                ->where('nik',$row['nik'])
                                                ->get();

        if($existingRow->isNotEmpty())
        {
            $this->dupData[$row['nik'] . '|' . $formatDate] = ($this->dupData[$row['nik'] . '|' . $formatDate] ?? 0) + 1;
            $this->duplicateRow++;
            $this->allData++;
            return null;            
        }


        
        $this->successInsert++;
        $this->allData++;
        return new KeluargaBerencana([
            'nik' => $row['nik'],
            'tanggal_kunjungan' => Date::excelToDateTimeObject($row['tanggal_kunjungan']),
            'umur' => $row['umur'],
            'jumlah_anak' => $row['jumlah_anak'],
            'akseptor' => $row['akseptor'],
            'mow' => empty(trim($row['mow'])) ? null : trim($row['mow']),
            'iud' => empty(trim($row['iud'])) ? null : trim($row['iud']),
            'suntik' => empty(trim($row['suntik'])) ? null : trim($row['suntik']),
            'pil' => empty(trim($row['pil'])) ? null : trim($row['pil']),
            'kondom' => empty(trim($row['kondom'])) ? null : trim($row['kondom']),
            'kunjungan' => $row['kunjungan'],
            'empat_T' => $row['4t'],
            'alki' => $row['alki'],
            'efek_samping' => $row['efek_samping'],
            'komplikasi' => $row['komplikasi'],
            'keterangan' => $row['keterangan'],
        ]);
    }

    public function rules(): array
    {
        return [
            // 'nama' => 'required',
            // 'nik' => 'numeric',      
        ];
    }

    public function getCountNoNik() 
    {
        return $this->countNoNik;
    }

    public function getNoData() 
    {
        return $this->noData;
    }

    public function getDupData() 
    {
        return $this->dupData;
    }

    public function getCountNoData() 
    {
        return $this->countNoData;
    }

    public function getDuplicateRow() 
    {
        return $this->duplicateRow;
    }

    public function getSuccessInsert() 
    {
        return $this->successInsert;
    }

    public function getAllData() 
    {
        return $this->allData;
    }
}
