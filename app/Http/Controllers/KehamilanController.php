<?php

namespace App\Http\Controllers;

use App\Imports\kehamilanImport;
use Illuminate\Http\Request;
use App\Models\Kehamilan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KehamilanController extends Controller
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

        
        // $tahunnow = substr('3204105402830006', 10, 2);
        // return $tahunnow;
        // if($grav) {
        //     $grav = ' 29 - 39 ';
        //     if (preg_match('/^([^\-]+)(?:\-(.+))?/', $grav, $matches)) {
        //         // $matches[1] contains 'G1P0A0'
        //         // $matches[2] contains '36-37' or null if not present
        //         $gravida = isset($matches[2]) ? trim($matches[2]) : trim($matches[1]);
        //     } else {
        //         $gravida = $grav;
        //     }
        // // }

        // return $gravida;

        $kehamilan = Kehamilan::whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->get();
        // $kehamilan = Kehamilan::get();
        $kehamilanAll = Kehamilan::get();

        // return $kehamilan;
        #scoreboard
        // 1. jumlah kunjungan
        $kunjungan = $kehamilan->count();
        // return $kunjungan;

        // 2. jumlah ibu hamil
        $ibuHamil = $kehamilan->unique('nik')->count();
        // return $ibuHamil;

        // 3. rerata umur ibu hamil
        $rUmur = Kehamilan::select(DB::raw('umur'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->get();
        // return $rUmur;
        $rerata = (int)$rUmur->avg('umur');

        #grafik umur
        //tambah label untuk id drilldown di grafik 
        $umur = Kehamilan::select(DB::raw('umur, COUNT(*) as count'))->whereBetween('tanggal_kunjungan', [$dateFrom, $dateTo])->orderBy('umur', 'asc')->groupBy('umur')->get();
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

        # grafik resti
        $resti = collect($kehamilan)->unique('nik');
        // return $resti;
        $drillResti = collect($resti)->unique('resti')->unique('nik')->map(function ($item) use ($kehamilan) {
            $count = $kehamilan->where('resti', $item['resti'])->count();
            $status = $item['resti'] == null ? 'Tidak Ada Resiko' : 'Ada Resiko';
            return [
                'resti' => $item['resti'],
                'count' => $count,
                'status' => $status,
            ];
        })->values();

        // return $drillResti;

        
        $mainResti = $drillResti->unique('status')->map(function ($item) use ($drillResti) {
            $count = $drillResti->where('status', $item['status'])->sum('count');
            return [
                'status' => $item['status'], 
                'count' => $count,
            ];
        })->values();

        # grafik LILA normal dan tidak
        $drillLila = collect($kehamilan)->unique('lila')->map(function ($item) use ($kehamilan) {
            $count = $kehamilan->where('lila', $item['lila'])->count();
            $status = $item['lila'] >= 23.5 ? 'Lebih dari 23.5 cm' : 'Kurang dari 23.5 cm';
            return [
                'lila' => (string)$item['lila'] . ' cm',
                'count' => $count,
                'status' => $status
            ];
        })->values();    
        
        $mainLila = $drillLila->unique('status')->map(function($item) use ($drillLila) {
            $count = $drillLila->where('status', $item['status'])->count();
            return [
                'status' => $item['status'],
                'count' => $count
            ];
        })->values();

        // option 2 grafik LILA (scatter)
        $scatterLila = $kehamilan->map(function($item) {
            if(!is_null($item->bb) && !is_null($item->lila)) {
                return [
                    // 'name' => $item->nama_bayi,
                    'x' => $item->bb,
                    'y' => $item->lila,
                ];
            }
    
            return null;
         })->filter()->values();
        // return $scatterData;
        
        // return $mainResti;

        #GPA (Primigravida atau multigravida)

        // buat kategori range umur ibu hamil
        $hamil = collect($kehamilan)->unique('nik');
        // return $hamil;
        $detailGPA = $hamil->map(function ($item) {
            $umur = $item['umur'];
            $range = $umur % 10;
            $label = $range < 5 ? ($umur - $range) . '-' . ($umur - $range + 4) : ($umur - $range+5) . '-'. ($umur - $range + 9);
            $gravida = substr($item['gpa'], 0, 2);

            $cekGravida = $gravida === 'G1' ? 'Primigravida' : 'Multigravida';
            return [
                'label' => $label,
                'umur' => $item['umur'],
                'gravida' => $cekGravida,
                'gpa' => $item['gpa']
            ];
        })->sortByDesc('umur')->values(); 
        
        // dd($detailGPA);

        // Get all unique labels and gravidas
        $allLabels = $detailGPA->pluck('label')->unique()->toArray();
        $allGravidas = $detailGPA->pluck('gravida')->unique()->toArray();

        // Generate all possible combinations
        $combinations = [];
        foreach ($allLabels as $label) {
            foreach ($allGravidas as $gravida) {
                $combinations[] = ['label' => $label, 'gravida' => $gravida];
            }
        }

        // Fill in the counts
        // kalo nulll tetep masuk tapi count nya 0
        $GPA = collect($combinations)->map(function ($combination) use ($detailGPA) {
            $count = $detailGPA
                ->where('label', $combination['label'])
                ->where('gravida', $combination['gravida'])
                ->count();

            return [
                'label' => $combination['label'],
                'gravida' => $combination['gravida'],
                'count' => $count,
            ];
        });

        

        $rangeUmur = $GPA->unique('label')->map(function ($item) {
            return [
                'label' => $item['label']
            ];
        })->values();

        foreach ($rangeUmur as $key => $value) {
            $rangeUmur[$key] = $value['label'];
        }
        
        
        $dataGPA = [
            ['name' => 'Primigravida', 'data' => []],
            ['name' => 'Multigravida', 'data' => []],
        ];
        
        // Iterate through the dataToPush array
        foreach ($GPA as $item) {
            // Find the index of the corresponding 'gravida' in $data
            $index = array_search($item['gravida'], array_column($dataGPA, 'name'));
            // print_r($index);
            // If the 'gravida' is found, push the count to the 'data' array
            if ($index !== false) {               
                if ($index == 0)
                {
                    $dataGPA[$index]['data'][] = ($item['count']);
                } else {
                    $dataGPA[$index]['data'][] = ($item['count']);
                }
            }
        }

        

        // return $dataGPA;


        // return $kehamilan;
        
        return view('pelayanan.kehamilan', [
            'kehamilan' => $kehamilanAll,
            'awal' => $dateFrom,
            'akhir' => $dateTo,
            'kunjungan' => $kunjungan,
            'ibuHamil' => $ibuHamil,
            'rerata' => $rerata,
            'mainAges' => $mainAges,
            'drillAges' => $drillAges,
            'drillResti' => $drillResti,
            'mainResti' => $mainResti,
            'mainLila' => $mainLila,
            'drillLila' => $drillLila,
            'scatterLila' => $scatterLila,
            'gpa' => $GPA,
            'detailGpa' => $detailGPA,
            'dataGPA' => $dataGPA,
            'rangeUmur' => $rangeUmur,
        ]);
    }

    public function import(Request $request)
    {
        // $data = Excel::import(new KBImport, request()->file('file'));

        $file = $request->file('file')->store('public/import');

        $import = new kehamilanImport;
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
