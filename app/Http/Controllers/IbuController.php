<?php

namespace App\Http\Controllers;

use App\Exports\IbuExport;
use App\Imports\IbuImport;
use App\Models\Ibu;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

class IbuController extends Controller
{
    public function index(){
        $ibu = Ibu::all();

        $age = $ibu->map(function ($item) {

            $tahunUmur = (int)substr($item['nik'], 10, 2);
            $tahunnow = Carbon::now()->startOfYear();
            $tahunSekarang = (int)substr($tahunnow, 0, 2);
            // $sysTahun = (int)substr($tanggalKelahiran, 2, 2);
            $umur = $tahunUmur < $tahunSekarang ? $tahunSekarang - $tahunUmur : $tahunSekarang - $tahunUmur + 100;

            return [
                'umur' => $umur
            ];

        })->values();

        // return $age;
        // // ambil umur dari nik
        // $tahunUmur = (int)substr($nik, 10, 2);
        // $tahunnow = Carbon::now()->startOfYear();
        // $sysTahun = (int)substr($tanggalKelahiran, 2, 2);
        // END ambil umur dari nik
    
        // hitung umur 
        // $umur = $tahunUmur < $sysTahun ? $sysTahun - $tahunUmur : $sysTahun - $tahunUmur + 100;
        return view('ibu', [
            'Ibu' => Ibu::orderBy('nama', 'asc')->get()
        ]);
    }

    public function import(Request $request){
        // Excel::import(new IbuImport, request()->file('file'));
        $file = $request->file('file')->store('public/import');

        $import = new IbuImport;
        $import->import($file);

        $countData = $import->getCountData();
        // return $countData;
        $duplicateRow = $import->getDuplicateRow();
        $successInsert = $import->getSuccessInsert();
        $nikNull = $import->getNIKnull();
        $allData = $import->getAllData();

        return back()->with('success', 'Data berhasil diimport. Data dalam Excel: ' . $allData . '<ul>                                
                                            <li>Total data baru: ' . $successInsert . '.</li>
                                            <li> Total data sama: ' . $duplicateRow . '.</li>
                                            <li>Total NIK salah format / kosong: ' . $nikNull . '</li> </ul>');
    }

    public function export(){
        return Excel::download(new IbuExport, 'anak.xlsx');
    }    
}
