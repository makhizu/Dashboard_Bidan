<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use Illuminate\Support\Str;
use App\Models\ImunisasiDTL;
use App\Models\ImunisasiHDR;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Imports\ImunisasiDTLImport;
use App\Imports\ImunisasiHDRImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImunisasiController extends Controller
{
    protected $kunjungan = 0;
    protected $mostVacin = [];

    public function index(Request $request) {

        // filter tanggal
        if($request->hasAny(['awal', 'akhir']))
        {
            $dateFrom = $request['awal'];
            $dateTo = $request['akhir'];
            
            if($dateFrom > $dateTo) 
            {
                // $today = Carbon::now();
                // $awal = Carbon::now()->startOfMonth();

                // $dateFrom = $awal->format('Y-m-d');
                // $dateTo = $today->format('Y-m-d');

                return back()->with('error', 'Mulai Tanggal <b>Tidak Boleh</b> Lebih Dari Sampai Tanggal');
            }


            
            // return $dateFrom;
        } else 
        {
            $today = Carbon::now();
            $awal = Carbon::now()->startOfMonth();

            $dateFrom = $awal->format('Y-m-d');
            $dateTo = $today->format('Y-m-d');
        }

        // $string = 'PENTABIO 1, POLIO 1. IPV 2';
        // $part = preg_split("/, |\. /", $string);
        // $parts = array_filter($part);
        // print_r($parts);
        // $array = explode(',', $string);

        // $array = array_map('trim', $array);

        // return $array;
        // return ImunisasiHDR::with('detail')->get();

        $comparison = new \Atomescrochus\StringSimilarities\Compare();                
        // echo $comparison->similarText('TD3', 'TD3');

        
        # data untuk grafik jenis imunisasi

        //ambil data jenis imunisasi dan juga jumlah nya
        // PENTABIO = 5
        // POLIO = 3
        // HB0 = 4


        $headerData = ImunisasiHDR::whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->get();
        // return $headerData;
        $headerId = $headerData->pluck('id');
        $detail = ImunisasiDTL::whereIn('id_header', $headerId)->select(DB::raw('imunisasi, COUNT(*) as count'))->orderBy('imunisasi', 'asc')->groupBy('imunisasi')->get();
        // return $detail;

        #tambah label untuk id drilldown di grafik 
        $drillImunisasi = $detail->map(function ($item) {
            return [
                'imunisasi' => $item['imunisasi'], 
                'count' => $item['count'], 
                'label' => explode(' ', trim($item['imunisasi']))[0] 
            ];            
        })->values();

        // return $drillImunisasi;
        
        #buat array baru untuk grafik utama
        $mainImunisasi = $drillImunisasi->unique('label')->map(function ($item) use ($drillImunisasi) {
            $count = $drillImunisasi->where('label', $item['label'])->sum('count');
            return [
                'label' => $item['label'], 
                'count' => $count
            ];
        })->values();

        // return $mainImunisasi;

        $idAnak = $headerData->sortBy('id_anak')->unique('id_anak')->pluck('id_anak');
        
        $mainTempatLahir = Anak::whereIn('id', $idAnak)
                            ->select(DB::raw('tempat_lahir, COUNT(*) as count'))
                            ->orderBy('tempat_lahir', 'asc')
                            ->groupBy('tempat_lahir')
                            ->get();

        
        $drillTempatLahir = Anak::whereIn('id', $idAnak)
                                ->select(DB::raw('tempat_lahir, jenis_kelamin, COUNT(*) as count'))
                                ->groupBy('tempat_lahir', 'jenis_kelamin')
                                ->get();
        $drillTempatLahir = $drillTempatLahir->map(function ($item) {
            if ($item['jenis_kelamin'] == 'P') {
                return [
                    'tempat_lahir' => $item['tempat_lahir'],
                    'count' => $item['count'],
                    'jenis_kelamin' => 'Perempuan',
                ];
            } else {
                return [
                    'tempat_lahir' => $item['tempat_lahir'],
                    'count' => $item['count'],
                    'jenis_kelamin' => 'Laki - laki',
                ];
            }
        });
        
        // grafik scatter sebaran BB per Umur Bayi
        // $scatterDataAwal = $headerData->map(function($item) {
        //         return [
        //             'tanggal_kunjungan' => $item->tanggal_kunjungan,
        //             'bb' => $item->bb,
        //             'nama_bayi' => $item->anak->nama_bayi,
        //             'tanggal_lahir' => $item->anak->tanggal_lahir,
        //         ];
        //     })->filter()->values();

        // return $scatterDataAwal;
    
        // $umur = $headerData->pluck('umur');
        // // Match sequences of digits or non-digits
        // $pattern = '/(\d+|\D+)/';

        // $split_umur = preg_split($pattern, $umur, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        // // $umur = array_filter($umur);
        // return $umur;

        $scatterData = $headerData->map(function($item) {
            // $kunjungan = Carbon::parse($item->tanggal_kunjungan);
            // $lahir = Carbon::parse($item->anak->tanggal_lahir);

            // $umur = $kunjungan->diffInMonths($lahir);
            $umur = str_replace(' ', '', $item->umur);
            $pattern = '/(\d+|\D+)/';

            $split_umur = collect(preg_split($pattern, $umur, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE));
            
            $age = 0;
            for ($i=0; $i < $split_umur->count(); $i += 2) { 
                $value = (int)$split_umur[$i];
                $unit = strtoupper($split_umur[$i + 1]);

                switch ($unit) {
                    case 'TH':
                        $value *= 12;
                        break;
                    
                    case 'THN':
                        $value *= 12;
                        break;
                    
                    case 'BLN':
                        $value *= 1;
                        break;
                    
                    case 'TAHUN':
                        $value *= 12;
                        break;
                    
                    case 'BULAN':
                        $value *= 1;
                        break;
                    
                    default:
                        $value *= 0;
                        break;
                }

                $age += $value;

            }

            return [
                // 'tanggal_kunjungan' => $item->tanggal_kunjungan,
                'y' => round($item->bb / 1000, 2),
                'nama_bayi' => $item->anak->nama_bayi,
                // 'tanggal_lahir' => $item->anak->tanggal_lahir,
                'x' => $age
            ];
        })->filter()->values();



        // return $scatterData; 


        
        $header = ImunisasiHDR::with('anak');

        # data scoreboard
        // 1. kunjungan
        $kunjungan = ImunisasiHDR::whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->count();
        // echo $kunjungan;
        
        // 2. vaksin terpopuler
        // $mostVacin['label'] = "null";
        $mostVacin = $mainImunisasi->sortByDesc('count')->first();
        // return $mostVacin;

        // 3. jumlah anak laki laki
        // 4. jumlah anak perempuan
   
        $imunisasiData = ImunisasiHDR::whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->pluck('id_anak')->unique();
        
        $laki = 0;
        $perempuan = 0;
        
        foreach ($imunisasiData as $imunisasi) {
            // Accessing the 'nama' attribute from the related Anak model
            $jk = ImunisasiHDR::where('id_anak', $imunisasi)->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->first()->anak->jenis_kelamin;

                if ($jk === 'L') {
                    $laki++;
                } elseif (($jk === 'P')) {
                    $perempuan++;
                }

        // Now you can use $namaAnak as needed
        // echo "Nama Anak: $namaAnak<br>";
        }
        // return $perempuan;
        

        // return $mainImunisasi;
        // $row = 'POLIO 2+ PCV 1';
        // $imunisasi = preg_split("/, |\. |\+ | \+ /", $row);
        // $imunisasi_trim = array_map('trim', $imunisasi);
        // $imunisasi_trim = array_filter($imunisasi_trim);

        // return $imunisasi_trim;
        
        return view('pelayanan.imunisasi', [
            'header' => ImunisasiHDR::all(),
            'drillImunisasi' => $drillImunisasi,
            'mainImunisasi' => $mainImunisasi,
            'kunjungan' => $kunjungan,
            'mostVacin' => $mostVacin,
            'laki' => $laki,
            'perempuan' => $perempuan,
            'awal' => $dateFrom,
            'akhir' => $dateTo,
            'mainTempatLahir' => $mainTempatLahir,
            'drillTempatLahir' => $drillTempatLahir,
            'scatterData' => $scatterData,
        ]);

    }

    public function import(Request $request)
    {
        // Validate the file
        // $request->validate([
        //     'file' => 'required|mimes:xlsx,xls',
        // ]);
        
        // Store the file
        $file = $request->file('file')->store('public/import');

        // $import = new ImunisasiHDRImport;
        // $import->import($file);
        // Import data into ImunisasiHDR
        $hdrImport = new ImunisasiHDRImport;
        $header = Excel::import($hdrImport, storage_path("app/{$file}"), null, \Maatwebsite\Excel\Excel::XLSX);

        // Get stored headers
        $headers = $hdrImport->getHeaders();
        // dd($headers);

        // return $headers;
        if(!empty($headers))
        {
            // // Import data into ImunisasiDTL
            $dtlImport = new ImunisasiDTLImport($headers);
            Excel::import($dtlImport, storage_path("app/{$file}"), null, \Maatwebsite\Excel\Excel::XLSX);
            $detail = $dtlImport->getDetail();
        }

        
        $allData = $hdrImport->getAllData();
        $successInsert = $hdrImport->getSuccessInsert();
        $duplicateRow = $hdrImport->getDuplicateRow();
        $noData = $hdrImport->getNoData();
        $countData = count($hdrImport->getCountData());
        $arraycountData = $hdrImport->getCountData();
        // return $arraycountData;

        // $countDataString = implode(', ', $countData);

        return redirect()->back()->with('success', 'Data berhasil diimport. Data dalam Excel: ' . $allData . '<ul>                                
        <li>Total data berhasil ditambah: ' . $successInsert . '.</li>
        <li>Total data sama: ' . $duplicateRow . '.</li>
        <li>Total data invalid: ' . $noData . '</li>
        <li>Total data anak invalid: ' . $countData . '</li> </ul>');
    }
}
