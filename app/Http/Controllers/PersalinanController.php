<?php

namespace App\Http\Controllers;

use App\Imports\persalinanImport;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Ibu;
use App\Models\Persalinan;
use Illuminate\Support\Facades\DB;

class PersalinanController extends Controller
{
    public function index(Request $request)
    {
        $rujuk = 0; //ibu yang dirujuk
        $normal = 0; //ibu melahirkan di bidan
        $rerata = 0; //rerata umur ibu
        $rerataPersalinan = 0; //rerata umur ibu
        
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
        
        $persalinan = Persalinan::whereBetween('tanggal_persalinan', [$dateFrom, $dateTo])->get();
        $persalinanAll = Persalinan::get();
        // $umur = $persalinan->pluck('umur');

        #grafik umur
        //tambah label untuk id drilldown di grafik 
        $umur = Persalinan::select(DB::raw('umur, COUNT(*) as count'))->whereBetween('tanggal_persalinan', [$dateFrom, $dateTo])->orderBy('umur', 'asc')->groupBy('umur')->get();
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
        # END umur

        # grafik komplikasi persalinan (bidan dan rujuk)
        $row = 'SEROTONIN & OBLIGINON';
        $imunisasi = preg_split("/, |\. |\+ |\& | \+ /", $row);
        $imunisasi_trim = array_map('trim', $imunisasi);
        $imunisasi_trim = array_filter($imunisasi_trim);

        // return $imunisasi_trim;

        // foreach ($imunisasi_trim as $key => $value) {
        //     return $value;
        // }

        // $komplikasi = $persalinan->map(function ($item) use ($persalinan) {
            
        //     $stat = $item['stat_rujuk_ibu'] == 'c' ? 'Rujuk' : 'Normal';

        //     $komplikasi_trim = [];
        //     $row = $item['komplikasi'];
        //     $komplikasi = preg_split("/, |\. |\+ |\& | \+ /", $row);
        //     $komplikasi_trim = array_map('trim', $komplikasi);
        //     $komplikasi_trim = array_filter($komplikasi_trim);
        //     // $komplikasi_trim = str_replace()
        //     $i = 1;
        //     if (!empty($item['komplikasi'])) {
        //         foreach ($komplikasi_trim as $key) {
        //           $cek[] = [
        //             'komplikasi' => $key,
        //             'stat' => $stat,
        //             // 'i' => $i,
        //           ];
        //           $i++;
        //         }
        //         return $cek;
        //       }
            
        //     // if ($cek->isNotEmpty()) {
        //     //     return $cek;
        //     // }else {

        //         return null;
        //     // }
            
        // })->filter()->values();
        // $komplikasi = collect($komplikasi)->flatten(1)->toArray();
        // $komplikasi = collect($komplikasi);

        // return $komplikasi;

        $drillKomp = $persalinan->map(function ($items) use ($persalinan) {
            $count = $persalinan->where('komplikasi', $items['komplikasi'])->where('stat_rujuk_ibu', $items['stat_rujuk_ibu'])->count();
            $stat = $items['stat_rujuk_ibu'] == 'c' ? 'Rujuk' : 'Normal';
            if ($items['komplikasi'] != null) {
                # code...
                return [
                    'komplikasi' => $items['komplikasi'], 
                    'count' => $count, 
                    'label' => $stat 
                ];  
            }

            // return null;
        })->unique()->filter()->values();

        // dd ($drillKomp);
        
        // return $persalinan;

        $mainKomp = $persalinan->unique('stat_rujuk_ibu')->map(function ($item) use ($persalinan) {
            // $stat_persalinan = $persalinan->where('stat_rujuk_ibu' , $item['stat_rujuk_ibu'])->first();
            $stat_persalinan = $persalinan->where('stat_rujuk_ibu' , $item['stat_rujuk_ibu'])->count();                        
            // $label = 
            $stat = $item['stat_rujuk_ibu'] == 'c' ? 'Rujuk' : 'Normal';
            return [
                'y' => $stat_persalinan,
                'name' =>  $stat,
                'drilldown' => $stat,
            ];
        })->sortBy('name')->values();

        // return $mainKomp;
        # END grafik komplikasi persalinan (bidan dan rujuk)

        # score board
        $rujuk = $persalinan->where('stat_rujuk_ibu' , 'c')->count();
        $normal = $persalinan->whereNull('stat_rujuk_ibu')->count();
        $rerata = $persalinan->avg('umur');
        $rerataPersalinan = $persalinan->avg('usia_kehamilan');
        # END score board

        #GPA (Primipara atau multipara)

        // buat kategori range umur ibu persalinan
        $persalinan = collect($persalinan);
        $detailGPA = $persalinan->map(function ($item) {
            $umur = $item['umur'];
            $range = $umur % 10;
            $label = $range < 5 ? ($umur - $range) . '-' . ($umur - $range + 4) : ($umur - $range+5) . '-'. ($umur - $range + 9);
            $para = substr($item['gpa'], 2, 2);

            $cekpara = $para === 'P0' ? 'Primipara' : 'Multipara';
            return [
                'label' => $label,
                'umur' => $item['umur'],
                'para' => $cekpara,
                'gpa' => $item['gpa']
            ];
        })->sortByDesc('umur')->values();  
        
        // return $detailGPA;

        // Get all unique labels and paras
        $allLabels = $detailGPA->pluck('label')->unique()->toArray();
        $allparas = $detailGPA->pluck('para')->unique()->toArray();

        // Generate all possible combinations
        $combinations = [];
        foreach ($allLabels as $label) {
            foreach ($allparas as $para) {
                $combinations[] = ['label' => $label, 'para' => $para];
            }
        }
        // return $combinations;

        // Fill in the counts
        // kalo nulll tetep masuk tapi count nya 0
        $GPA = collect($combinations)->map(function ($combination) use ($detailGPA) {
            $count = $detailGPA
                ->where('label', $combination['label'])
                ->where('para', $combination['para'])
                ->count();

            return [
                'label' => $combination['label'],
                'para' => $combination['para'],
                'count' => $count,
            ];
        });

        // return $GPA;

        

        $rangeUmur = $GPA->unique('label')->map(function ($item) {
            return [
                'label' => $item['label']
            ];
        })->values();

        foreach ($rangeUmur as $key => $value) {
            $rangeUmur[$key] = $value['label'];
        }
        
        
        $dataGPA = [
            ['name' => 'Primipara', 'data' => []],
            ['name' => 'Multipara', 'data' => []],
        ];
        
        // Iterate through the dataToPush array
        foreach ($GPA as $item) {
            // Find the index of the corresponding 'para' in $data
            $index = array_search($item['para'], array_column($dataGPA, 'name'));
            // print_r($index);
            // If the 'para' is found, push the count to the 'data' array
            if ($index !== false) {               
                // if ($index == 0)
                // {
                //     $dataGPA[$index]['data'][] = ($item['count'] * -1);
                // } else {
                //     $dataGPA[$index]['data'][] = ($item['count']);
                // }
                     $dataGPA[$index]['data'][] = ($item['count']);
            }
        }
        // return $dataGPA;

        // return $dataGPA;
        # END GPA (Primipara atau multipara)

        #sebaran bb dan pb bayi lahir
        $scatterData = $persalinan->map(function($item) {
            if(!is_null($item->pb_bayi) && ($item->pb_bayi > 0) && ($item->stat_ibu != 'c')) {
                return [
                    // 'name' => $item->nama_bayi,
                    'y' => $item->pb_bayi,
                    'x' => $item->bb_bayi,
                ];
            }
    
            return null;
         })->filter()->values();

        //  return $scatterData;
        # END sebaran bb dan pb bayi lahir

        // $row = 50.8;
        // $pb = $row == '-' ? null : trim($row);
        // return $pb;

        // return $normal;
        return view('pelayanan.persalinan', [
            'awal' => $dateFrom,
            'akhir' => $dateTo,
            'rujuk' => $rujuk,
            'normal' => $normal,
            'rerata' => (int)$rerata,
            'rerataPersalinan' => (int)$rerataPersalinan,
            'scatter' => $scatterData,
            'drillAges' => $drillAges,
            'mainAges' => $mainAges,
            'gpa' => $dataGPA,
            'rangeUmur' => $rangeUmur,
            'mainKomp' => $mainKomp,
            'drillKomp' => $drillKomp,
        ]);
    }

    public function import(Request $request)
    {
        // $data = Excel::import(new KBImport, request()->file('file'));

        $file = $request->file('file')->store('public/import');

        $import = new persalinanImport;
        $import->import($file);

        $countNoData = $import->getCountNoData();
        $countDupData = $import->getDupData();
        $countNoNik = $import->getCountNoNik();
        // return $countData;
        $duplicateRow = $import->getDuplicateRow();
        $successInsert = $import->getSuccessInsert();
        $allData = $import->getAllData();
        // $nikNull = $import->getNIKnull(); 

        // print_r($countDupData);

        return back()->with('success', 'Data berhasil diimport. Data dalam Excel: ' . $allData . '<ul>                                
                                            <li>Total data berhasil ditambah: ' . $successInsert . '.</li>
                                            <li> Total data sama: ' . $duplicateRow . '.</li>
                                            <li> Total NIK tidak terdaftar: ' . $countNoData . '.</li>
                                            <li>Total NIK salah format / kosong: ' . $countNoNik . '</li> </ul>');
    }
}
