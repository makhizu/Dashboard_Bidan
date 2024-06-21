<?php

namespace App\Imports;

use App\Models\Anak;
use App\Models\Ibu;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// use Maatwebsite\Excel\Events\BeforeImport;

class AnakImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $allData = 0;
    protected $countData = [];
    protected $duplicateRow = 0;
    protected $successInsert = 0;
    protected $nikNull = 0;
    protected $noIbu = 0;
    protected $updateData = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->allData++;
        // alur import data excel
        // 1. cek kolom nik, jika kosong return null
        // 2. cek kelengkapan data pb_lahir bb_lahir tempat_lahir jika ketiganya kosong, return null
        // 3. cek data ibu didatabase berdasarkan kolom nik, jika data ibu tidak ada return null
        // 4. cek kolom nama_bayi jika kosong ganti jadi 'BY NY' + nama ibu
        // 5. cek data anak sudah ada atau blom
        //    5.1. data anak ada maka update data jika
        //         5.1.1. jika pb_lahir di database null tapi di excel ada datanya maka update data anak tsb
        //         5.1.2. jika nama pada database masih 'BY NY' dan pada excel sudah nama lengkap maka update nama anak
        //         5.1.3. jika nama pada database tidak selengkap nama di excel maka ganti jadi nama di excel (nama didatabase nama panggilan tetapi nama di excel nama lengkap)
        // 6. jika kolom pb_lahir null maka ganti dengan 0.0
        // 7. tambah data anak ke database

        $namaBayi = $row['nama_bayi'];
        $nik = $row['nik'];

        // cek colom nik di excel, jika null atau panjang nya kurang dari 16 maka return null
        if(((!$row['pb_lahir'] || $row['pb_lahir'] == '-' || $row['pb_lahir'] == 0) 
        && (!$row['bb_lahir'] || $row['bb_lahir'] == '-' || $row['bb_lahir'] == 0) 
        && (!$row['tempat_lahir'] || $row['tempat_lahir'] == '-')        
        )
            || !$nik || (strlen($nik) != 16)) {
            // format salah atau nik null
            $this->nikNull++;            
            return null;
        }

        // ambil data ibu
        $ibu = Ibu::where('nik', $row['nik'])->first();

        // cek daata ibu, jika data ibu tidak ada return null
        if (!$ibu) {
            // data ibu blom terdaftar
            $this->noIbu++;            
            return null;
        }
        
        if(!$namaBayi) {
            // jika kolom nama_bayi kosong ganti dengan 'BY NY' + nama ibu
            $namaIbu = $ibu->nama;
            $row['nama_bayi'] = 'BY NY ' . $namaIbu;
        }

        // update data jika data pb_lahir database null tapi di excel ada data

        //ubah format date excel ke laravel
        $date = Date::excelToDateTimeObject($row['tanggal_lahir']);
        $formatDate = $date->format('Y-m-d');

        $pb_lahir = trim($row['pb_lahir']);
        $existingRow = [];
        if(trim($row['pb_lahir']) != null) {
            // cari data sesuai data kolom di excel kecuali nama_bayi
            $existingRow = Anak::where('nik', $row['nik'])
            // ->where('nama_bayi', $row['nama_bayi'])
            ->where('pb_lahir', $row['pb_lahir'])
            ->where('bb_lahir', $row['bb_lahir'])
            ->where('tempat_lahir', $row['tempat_lahir'])
            ->where('jenis_kelamin', $row['jk'])
            ->where('tanggal_lahir', $formatDate)
            ->first();            
        } 
        
        if ($existingRow == null){        
            
            // cari data sesuai data kolom di excel kecuali nama_bayi dan pb_lahir
            $existingRow = Anak::where('nik', $row['nik'])
            // ->where('nama_bayi', $row['nama_bayi'])
            // ->where('pb_lahir', $row['pb_lahir'])
            ->where('bb_lahir', $row['bb_lahir'])
            ->where('tempat_lahir', $row['tempat_lahir'])
            ->where('jenis_kelamin', $row['jk'])
            ->where('tanggal_lahir', $formatDate)
            ->first();
        }


        // If the row with the same values already exists, skip inserting this row
        if ($existingRow) {
            
            $updatePB = 0;
            $updateNama = 0;

            // update data. pb_lahir ada datanya di excel, tipenya angka, dan pb_lahir di database null
            if(($row['pb_lahir'] && is_numeric($row['pb_lahir'])) && $existingRow->pb_lahir == null) {
                Anak::where('nik', $row['nik'])
                // ->where('nama_bayi', $row['nama_bayi'])
                ->where('tanggal_lahir', $formatDate)
                ->where('bb_lahir', $row['bb_lahir'])
                
                //pb_lahir di database null
                ->where('pb_lahir', 0,0)  
                //update data pb_lahir
                ->update(['pb_lahir' => $row['pb_lahir']]);
                $updatePB = 1;
            }

            // ubah nama bayi jika
            #kondisi : 
            // 1. Nama di database 'NY BY' nama ibu sedangkan di excel nama anak
            // Alur 1 : 
            // - compare nama database dengan 'NY BY + nama ibu', nilainya harus lebih dari 60
            // - compare nama database dengan nama excel, nilainya harus kurang dari 60, jika nilainya lebih dari 60 berarti nama excel 'BY NY + nama ibu'
            // 2. Nama di database nama panggilan sedangkan di excel nama lengkap
            // Alur 2 : 
            // - masukkan panjang karakter nama di database dan juga di excel, beda variabel
            // - bandingkan panjang namaDB dan namaEX, namaEX harus lebih besar dari namaDB
            // - compare kemiripan karakter namaDB dan namaEX, nilai compare harus lebih dari 30

            $nama_anak = str_replace(' ', '', $existingRow->nama_bayi);
            $nama_excel = str_replace(' ', '', $row['nama_bayi']);
            // compare dengan BY NY + nama ibu (blom ada nama anak jadi pake nama ibu)
            $nama_awal = 'BYNY'. $ibu->nama;

            $comparison = new \Atomescrochus\StringSimilarities\Compare();
            //$similar = $comparison->similarText('BAYIRIZKI', 'BAYIRIZKIFADILLAH');
            
            // compare panjang nama anak
            $namaDB = strlen($nama_anak);
            $namaEX = strlen($nama_excel);
            
            if(($comparison->similarText($nama_anak, $nama_awal) > 60) && ($comparison->similarText($nama_excel, $nama_awal) < 60)) { // Alur 1
                $this->countData[$row['nik']] = ($this->countData[$row['nik']] ?? 0) + 1;    
                Anak::where('nama_bayi', $existingRow->nama_bayi)
                ->where('tanggal_lahir', $formatDate)
                ->update(['nama_bayi' => trim($row['nama_bayi'])]);
                $updateNama = 1;
            } elseif(($namaEX > $namaDB) && ($comparison->similarText($nama_anak, $nama_excel) > 30)) { // Alur 2                
                Anak::where('nama_bayi', $existingRow->nama_bayi)
                ->where('tanggal_lahir', $formatDate)
                ->update(['nama_bayi' => trim($row['nama_bayi'])]);
                $updateNama = 1;             
            }

            if (($updateNama == 1 && $updatePB == 1) || ($updateNama == 1 && $updatePB == 0) || ($updateNama == 0 && $updatePB == 1)) {
                $this->updateData++;
            }

            $this->duplicateRow++;
            //  // Increment the count for this 'nama_bayi' and 'nik' combination
            //  $this->countData[$row['nama_bayi'] . '|' . $row['nik'] . '|' . $row['pb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['tempat_lahir']] = ($this->countData[$row['nama_bayi'] . '|' . $row['nik'] . '|' . $row['pb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['tempat_lahir']] ?? 0) + 1;
            return null;
        }

        $pb_bayi = $row['pb_lahir'] ?: 0.0;
        
        $anak = new Anak([
            'nama_bayi' => strtoupper(trim($row['nama_bayi'])),
            'nik' => trim($row['nik']),
            'tanggal_lahir' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']),
            'pb_lahir' => trim($pb_bayi),
            'bb_lahir' => trim($row['bb_lahir']),
            'tempat_lahir' => trim($row['tempat_lahir']),
            'jenis_kelamin' => trim($row['jk']),
        ]);
        $this->successInsert++;        
        $anak->save();
    }

    public function rules(): array
    {
        return [
            // 'nama_bayi' => 'required',
            // 'nik' => 'required',
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

    public function getUpdatedData()
    {
        return $this->updateData;
    }
    
    public function getAllData()
    {
        return $this->allData;
    }

    public function getNIKnull()
    {
        return $this->nikNull;
    }

    public function getNoIbu()
    {
        return $this->noIbu;
    }

    public function onError(\Throwable $e)
    {
        // Check if all rows have the same 'nama_bayi' and 'nik' values
        $isAllSame = count($this->countData) === 1;

        if ($isAllSame) {
            throw new \Exception('All rows have the same values for "nama_bayi" and "nik".');
        }
    }


    


}
