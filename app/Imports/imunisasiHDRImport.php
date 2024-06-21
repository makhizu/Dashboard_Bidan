<?php

namespace App\Imports;

use App\Models\ImunisasiHDR;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Anak;
use PhpOffice\PhpSpreadsheet\Shared\Date;



class imunisasiHDRImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    private $headers = [];
    protected $noData = 0;
    protected $successInsert = 0;
    protected $duplicateRow = 0;
    protected $allData = 0;
    protected $countData = [];

    public function model(array $row)
    {
        // dd($row);
        $this->allData++;

        # jika format data salah, nik null, dan atau jumalh karakter nik salah        
        
        if ( ((!$row['pb_lahir'] || $row['pb_lahir'] == '-' || $row['pb_lahir'] == 0) 
        && (!$row['bb_lahir'] || $row['bb_lahir'] == '-' || $row['bb_lahir'] == 0) 
        && (!$row['tempat_lahir'] || $row['tempat_lahir'] == '-')        
        )
        || !$row['nik'] || strlen($row['nik']) != 16 )  
        {
            $this->noData++;
            return null;
        }

        # data duplikat jika tanggal kunjungan, id_anak sama
        
        $date = Date::excelToDateTimeObject($row['tanggal_lahir']);
        $formatDate = $date->format('Y-m-d');
        $pb_lahir = trim($row['pb_lahir']);
        # ambil data anak jika ada pb_lahir
        if($pb_lahir && is_numeric($pb_lahir))
        {
            $anak = Anak::where('nik', $row['nik'])
            ->where('bb_lahir', $row['bb_lahir'])
            ->where('pb_lahir', $pb_lahir)
            ->where('tanggal_lahir', $formatDate)
            ->first();

            // dd($anak);
        } else {
            // ambil data anak jika pb_lahir null
            $anak = Anak::where('nik', $row['nik'])
            ->where('bb_lahir', $row['bb_lahir'])
            // ->where('pb_lahir', $pb_lahir)
            ->where('tanggal_lahir', $formatDate)
            ->first();
        }

        if(!$anak) {
            $this->countData[$row['nik'] . '|' . $pb_lahir . '|' . $row['bb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['tempat_lahir']] = ($this->countData[$row['nik'] . '|' . $pb_lahir . '|' . $row['bb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['tempat_lahir']] ?? 0) + 1;
            return null;
        }

        // ambil data tanggal kunjungan
        $date_kunjungan = Date::excelToDateTimeObject($row['tanggal_kunjungan']);
        $tanggal_kunjungan = $date_kunjungan->format('Y-m-d');
        $idAnak = $anak->id;
        // dd($tanggal_kunjungan);

        //cari data imunisasi berdasarkan id_anak dan tanggal kunjungan, jika ada berarti data duplikat
        $existingRow = ImunisasiHDR::where('id_anak', $idAnak)
                                    ->where('tanggal_kunjungan', $tanggal_kunjungan)
                                    ->first();
        // dd($existingRow);
                                    
        if($existingRow) {
            $this->duplicateRow++;
            return null;
        }

        // dd($anak);

        $imunisasi =  new ImunisasiHDR([
            'id_anak' => $anak->id,
            'nik' => $row['nik'],
            'tanggal_kunjungan' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_kunjungan']),
            'bb' => $row['bb'],
            'umur' => $row['umur']
        ]);

        $this->successInsert++;
        $imunisasi->save();
        $this->headers[] = $imunisasi;
        // dd($this->headers);

    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getAllData()
    {
        return $this->allData;
    }

    public function getNoData()
    {
        return $this->noData;
    }

    public function getDuplicateRow()
    {
        return $this->duplicateRow;
    }

    public function getSuccessInsert()
    {
        return $this->successInsert;
    }

    public function getCountData()
    {
        return $this->countData;
    }
}
