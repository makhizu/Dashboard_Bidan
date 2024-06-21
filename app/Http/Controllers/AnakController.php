<?php

namespace App\Http\Controllers;

use App\Exports\AnakExport;
use App\Models\Anak;
use App\Imports\AnakImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AnakController extends Controller
{
    public function index(){


        $anak = Anak::get();
        
        if (sizeof($anak)) {
            
        $AnakJK = [
            $anak->where('jenis_kelamin', 'P')->count(),
            $anak->where('jenis_kelamin', 'L')->count(),
        ];
        
        $categories = Anak::distinct()->pluck('jenis_kelamin');
    
        $categoryMapping = [
            'L' => 'Laki - laki',
            'P' => 'Perempuan',
        ];

        foreach ($categories as $key => $category)
        if(isset($categoryMapping[$category])) {
            $categories[$key] = $categoryMapping[$category];
        }
        
        // return $categories;

        $tempatLahir = Anak::distinct()->pluck('tempat_lahir');
        // return $categories;

        //  return $anak->where('tempat_lahir', $tempatLahir[0]);

        $result = Anak::select(DB::raw('jenis_kelamin, COUNT(*) as count'))->groupBy('jenis_kelamin')->get();

        // return $result;
        
        
        // $anakLahir = [
        //     $anak->where('tempat_lahir', $tempatLahir[0])->count(),
        //     $anak->where('tempat_lahir', $tempatLahir[1])->count(),
        // ];
        
        $anakLahir = [];

        foreach ($tempatLahir as $tempat) {
            $count = $anak->where('tempat_lahir', $tempat)->count();

            $anakLahir[] = $count;
        }

        // $coba = $anak->where('tempat_lahir', 'RS')->where('jenis_kelamin', 'P')->count();
        // return $coba;

        // return $anakLahir;
        
        // return $anakLahir;

        // $JKLahir = [];
        $nestedLahir = [];
        $jeniskelamin = Anak::distinct()->pluck('jenis_kelamin');
        
       foreach ($jeniskelamin as $JK) { 
            foreach ($tempatLahir as $place) {
                $count = $anak->where('jenis_kelamin', $JK)->where('tempat_lahir', $place)->count();
                $nestedLahir[$JK][$place] = $count;
            }
       }

       // data grafik scatter
    //    formatnya [pb_lahir, bb_lahir]
    // kalo pb_lahir null lewat

     #buat array baru untuk grafik utama
     $scatterData = $anak->map(function($item) {
        if(!is_null($item->pb_lahir) && ($item->pb_lahir > 0)) {
            return [
                // 'name' => $item->nama_bayi,
                'y' => $item->pb_lahir,
                'x' => $item->bb_lahir,
            ];
        }

        return null;
     })->filter()->values();

    //  return $scatterData;
    
    

    //    return $nestedLahir;

        // return $JKLahir;
        // $row = ;

        // if($row && is_numeric($row))
        // {

        //     $cekData = Anak::where('nik', '3204115507010003')
        //     ->where('nama_bayi', 'MIKAYLA HAFIZAH')
        //     ->where('tanggal_lahir', '2022-03-14')
        //     ->where('bb_lahir', 2805)
        //     ->whereNull('pb_lahir')
        //     ->first();

        //     echo $cekData;
        // }

        $namaDB = strlen('AHMAD');
                $namaEX = strlen('AHMAD SAEFUL');
                // $comparison = new \Atomescrochus\StringSimilarities\Compare();
                // if (($namaDB < $namaEX) && ($comparison->similarText($namaDB, $namaEX) > 30)) {
                //     // Anak::where('nama_bayi', 'AHMAD')
                //     // ->where('tanggal_lahir', '2022-08-22')
                //     // ->update(['nama_bayi' => 'AHMAD SAEFUL']);
                //     // return null;
                // }
                // echo $comparison->similarText('BARRAADIPRATAMA', 'BARRA');

                // echo strtoupper(' Laravel ');
                // echo strtoupper(trim("  some text with spaces  "));
                // echo strtoupper(trim(' Laravel '));            
    } else {
        $AnakJK = null;
        $categories = null;
        $anakLahir = null;
        $tempatLahir = null;
        $nestedLahir = null;
        $result = null;
        $scatterData = null;
    }

        return view('pasien.anak', [
            'Anak' => Anak::all(),                        
            'JK' => $AnakJK,
            'labelJK' => $categories,
            'AnakLahir' => $anakLahir,
            'TempatLahir' => $tempatLahir,
            'nestedLahir' =>$nestedLahir,
            'result' => $result,
            'scatter' => $scatterData,

            
        ]);
    }

    public function import(Request $request){
        // $data = Excel::import(new AnakImport, request()->file('file'));
        // // dd($data) ;
        // return back();

        $file = $request->file('file')->store('public/import');

        $import = new AnakImport;
        $import->import($file);

        $countData = $import->getCountData();
        $duplicateRow = $import->getDuplicateRow();
        $successInsert = $import->getSuccessInsert();
        $updateData = $import->getUpdatedData();
        $allData = $import->getAllData();
        $nikNull = $import->getNIKnull();
        $noIbu = $import->getNoIbu();
        // return $countData;

        // dd($countData);
        // foreach ($countData as $key) {
        //     echo $key;
        // }


        return back()->with('success', 'Data berhasil diimport. Data dalam Excel: ' . $allData . '<ul>                                
                                            <li>Total data baru: ' . $successInsert . '.</li>
                                            <li> Total data sama: ' . $duplicateRow . '.</li>
                                            <li> Total data update: ' . $updateData . '.</li>
                                            <li> Total ibu belum terdaftar: ' . $noIbu . '.</li>
                                            <li>Total NIK salah format / kosong: ' . $nikNull . '</li> </ul>');
        
    }

    public function export(){
        return Excel::download(new AnakExport, 'anak.xlsx');
    }
}
