<?php

namespace App\Imports;

use App\Models\ImunisasiDTL;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\ImunisasiHDR;
use App\Models\Anak;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class imunisasiDTLImport implements ToModel, WithHeadingRow
{

    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    private $headers;
    private $detail = [];

    public function __construct($headers)
    {
        $this->headers = $headers;
    }

    public function model(array $row)
    {
        # jika format data salah, nik null, dan atau jumalh karakter nik salah. return null
        
        if ( ((!$row['pb_lahir'] || $row['pb_lahir'] == '-' || $row['pb_lahir'] == 0) 
        && (!$row['bb_lahir'] || $row['bb_lahir'] == '-' || $row['bb_lahir'] == 0) 
        && (!$row['tempat_lahir'] || $row['tempat_lahir'] == '-')        
        )
        || !$row['nik'] || strlen($row['nik']) != 16 )  
        {            
            return null;
        }

        # cari data duplikat, jika tanggal kunjungan dan id_anak sama. return null
        
        $date = Date::excelToDateTimeObject($row['tanggal_lahir']);
        $formatDate = $date->format('Y-m-d');
        $pb_lahir = trim($row['pb_lahir']);

        $anak = [];
        # ambil data anak jika ada pb_lahir
        if($pb_lahir && is_numeric($pb_lahir))
        {
            $anak = Anak::where('nik', $row['nik'])
            ->where('bb_lahir', $row['bb_lahir'])
            ->where('pb_lahir', $pb_lahir)
            ->where('tanggal_lahir', $formatDate)
            ->first();

            // dd($anak);
        }  
        
        if ($anak == null){
            // ambil data anak jika pb_lahir null
            $anak = Anak::where('nik', $row['nik'])
            ->where('bb_lahir', $row['bb_lahir'])
            // ->where('pb_lahir', $pb_lahir)
            ->where('tanggal_lahir', $formatDate)
            ->first();
        }

        if(!$anak) {
            // $this->countData[$row['nik'] . '|' . $row['pb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['tempat_lahir']] = ($this->countData[$row['nik'] . '|' . $row['pb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['bb_lahir'] . '|' . $row['tempat_lahir']] ?? 0) + 1;
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
                                    
        if(!$existingRow) {         
            return null;
        }
        
        $DTLexists = ImunisasiDTL::where('id_header', $existingRow->id)->get();

        if($DTLexists->isNotEmpty()) {
            return null;
        }


        // Get the corresponding header for this row
        $currentHeader = array_shift($this->headers);
        
        $imunisasi = preg_split("/, |\. |\+ | \+ /", $row['jenis_imunisasi']);
        $imunisasi_trim = array_map('trim', $imunisasi);
        $imunisasi_trim = array_filter($imunisasi_trim);
        
        // Log::info("Current id Header" . $currentHeader->id);
        // dd($currentHeader->id);
        // dd($imunisasi_trim);
        // $id_header = $currentHeader->id_anak;1
        // $id = 2;
        
        foreach ($imunisasi_trim as $key => $value) {
            $imunisasi_dtl = new ImunisasiDTL([
                'id_header' => $currentHeader->id,
                'id_anak' => $currentHeader->id_anak,
                'imunisasi' => $value,
            ]);

            $imunisasi_dtl->save();
            $this->detail[] = $imunisasi_dtl;
        }

        }

        public function getDetail()
        {
            return $this->detail;
        }
}
