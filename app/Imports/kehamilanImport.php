<?php

namespace App\Imports;

use App\Models\Kehamilan;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Ibu;

class kehamilanImport implements ToModel, WithHeadingRow
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
        $this->allData++;

        $nik = $row['nik'];

        # cek null kosong atau jumlah nik tidak samadengan 16
        if (!$nik || strlen($nik) != 16) {
            $this->countNoNik++;            
            return null;
        }

        $data = Ibu::where('nik', $nik)->get();

        # cek data ibu ada atau tidak
        if($data->isEmpty()) {
            $this->countNoData++;            
            $this->noData[$row['nik']] = ($this->noData[$row['nik']] ?? 0) + 1;
            return null;
        }

        $date = Date::excelToDateTimeObject($row['tanggal']);
        $formatDate = $date->format('Y-m-d');

        // $existingRow = [];
        # cek data sudah masuk ke database atau belum
        $existingRow = Kehamilan::where('tanggal_kunjungan', $formatDate)
                                                ->where('nik',$row['nik'])
                                                ->get();

        #return null jika data sudah ada
        if($existingRow->isNotEmpty())
        {
            $this->dupData[$row['nik'] . '|' . $formatDate] = ($this->dupData[$row['nik'] . '|' . $formatDate] ?? 0) + 1;
            $this->duplicateRow++;            
            return null;            
        }

        # GRAVIDA DAN GPA
        $gpa = null;
        $grav = null;
        $gravida = null;
        // Match 'G1P0A0' and '36-37' with or without '/'
        if (preg_match('/^([^\/]+)(?:\/(.+))?/', $row['gpa_grav'], $matches)) {
            // $matches[1] contains 'G1P0A0'
            // $matches[2] contains '36-37' or null if not present

            $gpa = trim($matches[1]);
            $grav = isset($matches[2]) ? trim($matches[2]) : null;

            // Now $gpa contains 'G1P0A0' and $grav contains '36-37' or null if not present
        } else {
            // Handle the case where the string doesn't match the expected format
            return null;
        }

        if($grav) {
            if (preg_match('/^([^\-]+)(?:\-(.+))?/', $grav, $matches)) {
                // $matches[1] contains 'G1P0A0'
                // $matches[2] contains '36-37' or null if not present
                $gravida = isset($matches[2]) ? trim($matches[2]) : trim($matches[1]);
            } else {
                $gravida = $grav;
            }
        }
        # END GRAVIDA DAN GPA

        # BB DAN TB
        $bb = null;
        $tb = null;
        if (preg_match('/^([^\/]+)(?:\/(.+))?/', $row['bb_tb'], $matches)) {
            
            $bb = trim($matches[1]);
            $tb = isset($matches[2]) ? trim($matches[2]) : null;
        }

        # END BB DAN TB

        # LILA DAN IMT
        $lila = null;
        $imt = null;
        if (preg_match('/^([^\/]+)(?:\/(.+))?/', $row['lila'], $matches)) {
            $lila = trim($matches[1]);
            // $imt = isset($matches[2]) ? trim($matches[2]) : null;
        } else {
            $lila = trim($row['lila']);
        }

        // return $lila;

        # END LILA DAN IMT

        # keluhan
        $keluhan = trim($row['keluhan']) == '-' ? null : trim($row['keluhan']);

        # Resti
        $resti = trim($row['resti']) == '-' ? null : trim($row['resti']);

        $kehamilan = new Kehamilan([
            'nik' => $row['nik'],
            'tanggal_kunjungan' => $formatDate,
            'umur' => trim($row['umur']),
            'gpa' => $gpa,
            'gravida' => $gravida,
            'bb' => $bb,
            'tb' => $tb,
            'lila' => $lila,
            'keluhan' => $keluhan,
            'resti' => $resti,
            'tekanan_darah' => trim($row['tekanan_darah']),

        ]);
        
        $this->successInsert++;
        $kehamilan->save();

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
