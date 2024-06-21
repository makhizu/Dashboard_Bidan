<?php

namespace App\Http\Controllers;

use App\Imports\KBImport;
use App\Imports\IbuImport;
use App\Models\KeluargaBerencana;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Carbon;
use Atomescrochus\StringSimilarities\JaroWinkler;
// use Carbon\Carbon;

class KBController extends Controller
{
    public function index(Request $request)
    {   

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



        $akseptor = KeluargaBerencana::whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->get();

        $label = ['MOW', 'IUD', 'Suntik', 'Pil', 'Kondom'];


        $renamedArray = [];
        foreach ($label as $labels) {
            $renamedArray[] = ["label" => $labels];
        }

        $label = $renamedArray;

        // return $renamedArray;
        $dataAkseptor = [
            $akseptor->where('akseptor', 1)->count(),
            $akseptor->where('akseptor', 2)->count(),
            $akseptor->where('akseptor', 3)->count(),
            $akseptor->where('akseptor', 4)->count(),
            $akseptor->where('akseptor', 5)->count(),
        ];

        $array = [];
        $data = $dataAkseptor;
        foreach ($data as $datas) {
            $array[] = ["count" => $datas];
        }

        $data = $array;

        // $data = array_merge($label, $dataAkseptor);
        // return $data;

        # mengambil data dari kolom mow, iud, suntik, pil, dan kondom
        $mow = KeluargaBerencana::select(DB::raw('mow, COUNT(*) as count'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->whereNotNull('mow')->groupBy('mow')->get();
        $iud = KeluargaBerencana::select(DB::raw('iud, COUNT(*) as count'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->whereNotNull('iud')->groupBy('iud')->get();
        $suntik = KeluargaBerencana::select(DB::raw('suntik, COUNT(*) as count'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->whereNotNull('suntik')->groupBy('suntik')->get();
        $pil = KeluargaBerencana::select(DB::raw('pil, COUNT(*) as count'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->whereNotNull('pil')->groupBy('pil')->get();
        $kondom = KeluargaBerencana::select(DB::raw('kondom, COUNT(*) as count'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->whereNotNull('kondom')->groupBy('kondom')->get();

        # menambah value untuk menyesuaikan dengan chart yang akan dibuat
        // $mow = $mow->map(function ($item) {
            //     return ['akseptor' => 'mow', 'jenis' => $item['mow'], 'count' => $item['count']];
            // });
            
        #buat fungsi menyesuaikan data untuk chart
        $mow = $this->drilldownData($mow, 'mow');
        $iud = $this->drilldownData($iud, 'iud');
        $suntik = $this->drilldownData($suntik, 'suntik');
        $pil = $this->drilldownData($pil, 'pil');
        $kondom = $this->drilldownData($kondom, 'kondom');
        
        $currentData = array_merge($iud->toArray(), $mow->toArray(), $suntik->toArray(), $pil->toArray(), $kondom->toArray());
        // return $currentData;
        // $allAkseptor = KeluargaBerencana::all()->count();
        // return $allAkseptor;



        // return $currentData;
        // $result = Anak::select(DB::raw('jenis_kelamin, COUNT(*) as count'))->groupBy('jenis_kelamin')->get();

        // return KeluargaBerencana::select('akseptor')->groupBy('akseptor')->get();

        // $akseptorkb = KeluargaBerencana::groupBy('akseptor');
        // $akseptorkb->all();

        // dd($akseptorkb);
        // $categories = KeluargaBerencana::distinct()->pluck('akseptor');
        // return $categories;

        // dd($dataAkseptor);

        #grafik riwayat medis (keguguran)

        #hitung data yang tidak ada riwayat keguguran dan ada riwayat keguguran
        $aNegatif = KeluargaBerencana::whereRaw("RIGHT(jumlah_anak, 1) = '0'")->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->count();

        $aPositif = KeluargaBerencana::whereRaw("RIGHT(jumlah_anak, 1) != '0'")->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->count();

        // return $aPositif;

        #masukan data count ke array dan masukkan label nya

        $abortus[] = ["kategori" => "Ada Riwayat", "count" => $aPositif];

        $abortus[] = ["kategori" => "Tidak Ada Riwayat", "count" => $aNegatif];

        // return $abortus;
        #grafik kunjungan lama dan baru

        #hitung jumlah data kunjungan lama dan baru
        $kLama = KeluargaBerencana::where('kunjungan', 'l')->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->count();
        $kBaru = KeluargaBerencana::where('kunjungan', 'b')->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->count();

        #masukkan ke array dan juga labelnya

        $cKunjungan[] = ["kunjungan" => "Lama", "count" => $kLama];
        $cKunjungan[] = ["kunjungan" => "Baru", "count" => $kBaru];

        // return $cKunjungan;
        # buat di score card
        // 1. scoreboard kunjungan
        $allKunjungan = $aPositif + $aNegatif;
        // 2. scoreboard rerata umur ibu
        $rUmur = KeluargaBerencana::select(DB::raw('umur'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->get();
        // return $rUmur;
        $rerata = (int)$rUmur->avg('umur');
        // return $rerata;
        // 3. Jumlah Ibu KB
        $ibuAll = KeluargaBerencana::whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->get();
        $ibu = $ibuAll->unique('nik')->count();
        // return $ibu;
        // 4. KB populer
        
        $KBall = collect($currentData);
        // return $KBall;
        $KB = $KBall->unique('akseptor')->map(function ($item) use ($KBall) {
            $count = $KBall->where('akseptor', $item['akseptor'])->sum('count');
            return [
                'akseptor' => $item['akseptor'],
                'count' => $count
            ];
        })->sortByDesc('count')->values();
        // return $KB;        
        $mostKB = $KB->isNotEmpty() ? strtoupper($KB[0]['akseptor']) : null;;
        // return $mostKB;

        
        // $KB = collect($currentData)->unique('akseptor')->map(function ($item) use ($currentData) {
        //     $count = $currentData->where('akseptor', $item['akseptor'])->sum('count');
        //     return [
        //         'akseptor' => $item['akseptor'], 'count' => $count
        //     ];
        // })->values();

        // return $KB;

        // $mainImunisasi = $drillImunisasi->unique('label')->map(function ($item) use ($drillImunisasi) {
        //     $count = $drillImunisasi->where('label', $item['label'])->sum('count');
        //     return ['label' => $item['label'], 'count' => $count];
        // })->values();
        

        # grafik range umur
        
        #ambil data umur dan sorting ascending
        $umur = KeluargaBerencana::select(DB::raw('umur, COUNT(*) as count'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->orderBy('umur', 'asc')->groupBy('umur')->get();
        // return $umur; 
        
        #tambah label untuk id drilldown di grafik 
        $drillAges = $umur->map(function ($item) {
            return [
                'umur' => $item['umur'], 
                'count' => $item['count'], 
                'label' => (((int)($item['umur'] / 10)) * 10) . "-an" ];            
        })->values();

        #buat array baru untuk grafik utama
        $mainAges = $drillAges->unique('label')->map(function ($item) use ($drillAges) {
            $count = $drillAges->where('label', $item['label'])->sum('count');
            return [
                'label' => $item['label'], 
                'count' => $count
            ];
        })->values();

        // return $drillAges;
// 
        // dd($mainAges, $drillAges);
// 
        // return $umur;

        if($umur->isNotEmpty())
        {

            $firstData = $umur[0]['umur']; // mengambil data pertama
            $lastData = $umur[count($umur) - 1]['umur']; // mengambil data terakhir
            
            $result = [];
            for ($i = $firstData; $i <= $lastData; $i += 10) {
                $result[] = ["age" => $i, "nama" => $i . "an"];
            }
        }
        // return $result;

        // return (int)(29 / 10);

        // $str1 = 'BY NY NIDYA';
        // $str2 = 'BYAN HIDAYATULLOH';

        // similar_text($str1, $str2, $percent);

        // echo "Similarity: $percent%";

        // // or compare each character manually
        // $length = min(strlen($str1), strlen($str2));
        // $matchCount = 0;

        // for ($i = 0; $i < $length; $i++) {
        //     if ($str1[$i] === $str2[$i]) {
        //         $matchCount++;
        //     }
        // }

        // $matchPercentage = ($matchCount / $length) * 100;
        // echo "Similarity: $matchPercentage%";
        
        // $comparison = new \Atomescrochus\StringSimilarities\Compare();
        
        // $similar = $comparison->similarText('BAYIRIZKI', 'BAYIRIZKIFADILLAH');
        // echo "Similarity: $similar";

        // $BY = "RIZKI";
        // echo "BYNY" . $BY;

        //         $string = "    T  his is a string with     spaces   ";
        // $modifiedString = str_replace(' ', '', $string);
        // echo $modifiedString;
        
        // $jaroWinkler = new JaroWinkler;

        // $similarity = $jaroWinkler->compare('BAYI WINDA', 'WINDA BAYI');
        // echo $similarity;

        return view('pelayanan.kb', [
            'KB' => KeluargaBerencana::orderBy('tanggal_kunjungan', 'asc')->get(),
            'dataAkseptor' => $dataAkseptor,
            'label' => $label,
            'data' => $data,
            'drilldown' => $currentData,
            'cAbortus' => $abortus,
            'kunjungan' => $allKunjungan,
            'cKunjungan' => $cKunjungan,
            'mainAges' => $mainAges,
            'drillAges' => $drillAges,
            'rerata' => $rerata,
            'awal' => $dateFrom,
            'akhir' => $dateTo,
            'jumlah_ibu' => $ibu,
            'mostKB' => $mostKB,
        ]);
    }

    public function drilldownData($data, $akseptor)
    {
        return $data->map(function ($item) use ($akseptor) {
            return ['akseptor' => $akseptor, 'jenis' => $item[$akseptor], 'count' => $item['count']];
        });
    }


    public function import(Request $request)
    {
        // $data = Excel::import(new KBImport, request()->file('file'));

        $file = $request->file('file')->store('public/import');

        $import = new KBImport;
        $import->import($file);

        $countNoData = $import->getCountNoData();
        $countDupData = $import->getDupData();
        $countNoNik = $import->getCountNoNik();
        // return $countData;
        $duplicateRow = $import->getDuplicateRow();
        $successInsert = $import->getSuccessInsert();
        $allData = $import->getAllData();
        // $nikNull = $import->getNIKnull(); 

        // return $countDupData;

        return back()->with('success', 'Data berhasil diimport. Data dalam Excel: ' . $allData . '<ul>                                
                                            <li>Total data berhasil ditambah: ' . $successInsert . '.</li>
                                            <li> Total data sama: ' . $duplicateRow . '.</li>
                                            <li> Total NIK tidak terdaftar: ' . $countNoData . '.</li>
                                            <li>Total NIK salah format / kosong: ' . $countNoNik . '</li> </ul>');
    }
}
