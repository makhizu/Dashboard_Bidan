<?php

namespace App\Imports;

use App\Models\Persalinan;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Ibu;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Carbon;

class persalinanImport implements ToModel, WithHeadingRow
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

        // buat if clause nya kalo ada
        // - nik string (udah) $nik
        // - GPA string (udah) $row['gpa']
        // - usia Kehamilan int (udah) $gravida
        // - umur int, ambil dari nik (udah) $umur
        // - tanggal kelahiran date (udah) $tanggalKelahiran
        // - komplikasi string (udah) $komplikasi
        // - jk bayi string $row['jk']
        // - bb bayi int $row['bb']
        // - pb bayi int $row['pb']
        // - lk bayi int $row['lk']
        // - ld bayi int $row['ld']

        // JIKA DIRUJUK MASUKKAN NIK SAMA umur sama KOMPLIKASI SAMA STATUS AJA
        // - stat rujuk ibu string (udah) $statRujukIbu
        // - stat rujuk bayi string (udah) $statRujukBayi

        $this->allData++;
        $nik = $row['nik'];

        // jika nik kosong atau formatnya salah tidak 16 karakter
        if (!$nik || strlen($nik) != 16) {
            $this->countNoNik++;            
            return null;
        }

        $data = Ibu::where('nik', $nik)->get();

        // cek data nik sudah ada atau blum di tb ibu
        if($data->isEmpty()) {
            $this->countNoData++;            
            $this->noData[$row['nik']] = ($this->noData[$row['nik']] ?? 0) + 1;
            return null;
        }

        // return null jika tidak ada persalinan atau ibu di rujuk
        // kondisi kolom kf1 kosong dan kolom status rujukan kosong
        if ($row['kf1'] != 'c' && $row['stat_ibu'] != 'c') {
            return null;
        }

        // tanggal persalinan ibu
        $date = Date::excelToDateTimeObject($row['tanggal_persalinan']);
        $tanggalKelahiran = $date->format('Y-m-d');

        // END tanggal persalinan ibu

        // bb pb lk ld bayi
        $bb = $row['bb'] == '-' || !$row['bb'] ? null : trim($row['bb']);
        $pb = $row['pb'] == '-' || !$row['pb'] ? null : trim($row['pb']);
        $lk = $row['lk'] == '-' || !$row['lk'] ? null : trim($row['lk']);
        $ld = $row['ld'] == '-' || !$row['ld'] ? null : trim($row['ld']);
        $jk = $row['jk'] == '-' || !$row['jk'] ? null : trim($row['jk']);

        // $bb = $this->nullValue($row['bb']);
        // $pb = $this->nullValue($row['pb']);
        // $lk = $this->nullValue($row['lk']);
        // $ld = $this->nullValue($row['ld']);
        // $jk = $this->nullValue($row['jk']);
        // END bb pb lk ld bayi

        // cek data redundan
        $data_persalinan = Persalinan::where('nik', $nik)
                                     ->where('tanggal_persalinan', $tanggalKelahiran)
                                     ->where('bb_bayi', $bb)
                                     ->get();

        if ($data_persalinan->isNotEmpty()) {
            $this->duplicateRow++;
            return null;
        }

        // cek isi kolom komplikasi
        $komplikasi = trim($row['komplikasi']) == '-' || !$row['komplikasi']  ? null : trim($row['komplikasi']);

        // stat ibu
        $statRujukIbu = trim($row['stat_ibu']) == '-' || !$row['stat_ibu'] ? null : trim($row['stat_ibu']);
        // stat bayi
        $statRujukBayi = trim($row['stat_bayi']) == '-' || !$row['stat_bayi'] ? null : trim($row['stat_bayi']);

        
        // ambil umur dari nik
        $tahunUmur = (int)substr($nik, 10, 2);
        $tahunnow = Carbon::now()->startOfYear();
        $sysTahun = (int)substr($tanggalKelahiran, 2, 2);
        // END ambil umur dari nik
    
        // hitung umur 
        $umur = $tahunUmur < $sysTahun ? $sysTahun - $tahunUmur : $sysTahun - $tahunUmur + 100;
        
        // ambil umur kehamilan paling besar
        // case : 39-40
        $grav = $row['usia_kehamilan'];
        if ($grav) {
            if (preg_match('/^([^\-]+)(?:\-(.+))?/', $grav, $matches)) {
                // $matches[1] contains 'G1P0A0'
                // $matches[2] contains '36-37' or null if not present
                $gravida = isset($matches[2]) ? trim($matches[2]) : trim($matches[1]);
            } else {
                $gravida = trim($grav);
            }
        }
        // END hitung umur 
        
        $persalinan =  new Persalinan([
            'nik' => $nik,
            'komplikasi' => $komplikasi,
            'usia_kehamilan' => $gravida,
            'gpa' => trim($row['gpa']),
            'stat_rujuk_ibu' => $statRujukIbu,
            'stat_rujuk_bayi' => $statRujukBayi,
            'bb_bayi' => $bb,
            'pb_bayi' => $pb,
            'lk_bayi' => $lk,
            'ld_bayi' => $ld,
            'jk_bayi' => $jk,
            'umur' => $umur,
            'tanggal_persalinan' => $tanggalKelahiran,
        ]);

        $this->successInsert++;
        $persalinan->save();
    }

    public function nullValue($row)
    {
        if ($row = '-' || !$row) {
            return null;
        } else {
            return trim($row);
        }
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
