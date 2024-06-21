<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\KeluargaBerencana;
use App\Models\ImunisasiHDR;
use App\Models\ImunisasiDTL;
use App\Models\Kehamilan;
use App\Models\Persalinan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;

class HomeController extends Controller
{
    // protected $months = [1,2,3,4,5,6,7,8,9,10,11,12];
    protected $mostVacin = [];
    protected $jumlah = 0;
    protected $rerataMonth = 0;
    
    public function index(Request $request)
    {
        // filter tanggal
        if($request->hasAny('tahun'))
        {
            $year = (int)$request['tahun'];                        
        } else 
        {
            $today = Carbon::now();
            $awalTahun = Carbon::now()->startOfYear();
            $year = (int)substr($awalTahun, 0, 4);
        }
        // return $year;        

        // list filter tahun
        $yearList = [];
        $kbYear = KeluargaBerencana::get()->pluck('tanggal_kunjungan')->map(function ($date) {
            return Carbon::parse($date)->year;
          })->unique()->values()->toArray();        
        
        $imunisasiYear = ImunisasiHDR::get()->pluck('tanggal_kunjungan')->map(function ($date) {
            return Carbon::parse($date)->year;
          })->unique()->values()->toArray();

        $kehamilanYear = Kehamilan::get()->pluck('tanggal_kunjungan')->map(function ($date) {
            return Carbon::parse($date)->year;
          })->unique()->values()->toArray();

        $persalinanYear = Persalinan::get()->pluck('tanggal_persalinan')->map(function ($date) {
            return Carbon::parse($date)->year;
          })->unique()->values()->toArray();

        $listTahun = array_merge($kbYear, $imunisasiYear, $kehamilanYear, $persalinanYear);
        $yearList = collect($listTahun)->unique();
        
        // return $yearList;
        // END list filter tahun

        $kb = KeluargaBerencana::whereYear('tanggal_kunjungan', $year)->get();
        $imunisasi = ImunisasiHDR::whereYear('tanggal_kunjungan', $year)->get();
        $kehamilan = Kehamilan::whereYear('tanggal_kunjungan', $year)->get();
        $persalinan = Persalinan::whereYear('tanggal_persalinan', $year)->get();

        // return $kb;
        // $year = 2023;

        # diagram mix

        // chart batang
        // cari jumlah kunjjungan kb per bulan
        $kbData = $this->dataCount($year, $kb, 'KB');
        // return $kbData;
        // cari jumlah kunjjungan imunisasi per bulan        
        $imunisasiData = $this->dataCount($year, $imunisasi, 'Imunisasi');
        // cari jumlah kunjjungan kehamilan per bulan
        $kehamilanData = $this->dataCount($year, $kehamilan, 'Kehamilan');
        // cari jumlah kunjjungan persalinan per bulan
        $persalinanData = $this->dataCount($year, $persalinan, 'Persalinan');
        // return $persalinanData; 
        // end chart batang
        $kehamilanCount = collect($kehamilanData)->groupBy('count')->sum('count');
        

        $rata = array_merge($kbData, $imunisasiData, $kehamilanData, $persalinanData);
        $rata = collect($rata);
        // return $rata;
        // jumlah kunjungan per layanan
        $countLayanan = $rata->unique('name')->map(function ($item) use ($rata) {
            $count = $rata->where('name', $item['name'])->sum('count');
            return [
                'name' => $item['name'],
                'count' => $count
            ];
        })->values();

        // return $countLayanan;

        // cari jumlah kunjungan per bulan
        $jumlah = $rata->reduce(function ($acc, $curr) {
            return $acc + $curr['count'];
        });

        // return $jumlah;

        // cari rata rata kunjungan semua
        $rerata = $rata->unique('month')->map(function ($item) use ($rata) {
            $jumlah = $rata->where('month', $item['month'])->sum('count');
            $avg = $jumlah / 4;

            return [
                'month' => $item['month'],
                'rata' => ceil($avg)
            ];
        })->values();

        // return $rerata;
        $rerataMonth = ceil($rerata->sum('rata') / 12);
        
        // return $rerataMonth;

        // return $kbData;
        // return $imunisasiData;

        #imunisasi terbyanyak
        
        // return $headerData;
        $headerId = $imunisasi->pluck('id');
        $detailImunisasi = ImunisasiDTL::whereIn('id_header', $headerId)->select(DB::raw('imunisasi, COUNT(*) as count'))->orderBy('imunisasi', 'asc')->groupBy('imunisasi')->get();
        // return $detailImunisasi;

        #tambah label untuk id drilldown di grafik 
        $drillImunisasi = $detailImunisasi->map(function ($item) {
            return [
                'imunisasi' => $item['imunisasi'], 
                'count' => $item['count'], 
                'label' => explode(' ', trim($item['imunisasi']))[0] 
            ];            
        })->values();
        
        #buat array baru untuk grafik utama
        $mainImunisasi = $drillImunisasi->unique('label')->map(function ($item) use ($drillImunisasi) {
            $count = $drillImunisasi->where('label', $item['label'])->sum('count');
            return [
                'label' => $item['label'], 
                'count' => $count
            ];
        })->values();
        
        #scoreboard
        $mostVacin = $mainImunisasi->sortByDesc('count')->first();

        $detailKB = KeluargaBerencana::select(DB::raw('akseptor, COUNT(*) as count'))->whereYear('tanggal_kunjungan', $year)->orderBy('akseptor', 'asc')->groupBy('akseptor')->get();

        $chartKB = $detailKB->map(function ($item) {
            if ($item['akseptor'] == 1) {
                $label = 'MOW';
            } elseif ($item['akseptor'] == 2) {
                $label = 'IUD';
            } elseif ($item['akseptor'] == 3) {
                $label = 'Suntik';
            } elseif ($item['akseptor'] == 4) {
                $label = 'Pil';
            } elseif ($item['akseptor'] == 5) {
                $label = 'Kondom';
            } 
            return [
                'label' => $label,
                'count' => $item['count']
            ];
        })->values();

        $kbMOST = $chartKB->sortByDesc('count')->first();

        // if ($kbMOST) {
        //     return 'ada';
        // } else {
        //     return 'tidak';
        // }

        $mostKB = $kbMOST ? $kbMOST : null;

        // return $mostKB;

        // $drillKB = $kb->whereMonth('tanggal_kunjungan', '10')->get();
        // return $drillKB;
        // $monthlyData = $kb->groupBy(function ($item) {
        //     return \Carbon\Carbon::parse($item['tanggal_kunjungan'])->format('Y-m');
        // })->map(function ($items, $month) {
        //     return [
        //         'month' => $month,
        //         'akseptor' => $items['akseptor'],
        //     ];
        // })->values()->toArray();
        $monthlyDataKB = $kb->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item['tanggal_kunjungan'])->format('m');
        })->map(function ($items, $month) {
            $akseptorCounts = $items->groupBy('akseptor')->map(function ($akseptorItems, $akseptor) {
                if ($akseptor == 1) {
                    $label = 'MOW';
                } elseif ($akseptor == 2) {
                    $label = 'IUD';
                } elseif ($akseptor == 3) {
                    $label = 'Suntik';
                } elseif ($akseptor == 4) {
                    $label = 'Pil';
                } elseif ($akseptor == 5) {
                    $label = 'Kondom';
                }

                return [
                    'akseptor' => $label,
                    'count' => $akseptorItems->count(),
                ];
            })->values()->toArray();
            
            $label = $this->month($month);
            
            return [
                'month' => $label,
                'data' => $akseptorCounts,
            ];
        })->values()->toArray();

        // return $monthlyDataKB;
        

        // return $ChartKB ;

        $mainPersalinan = $persalinan->unique('stat_rujuk_ibu')->map(function ($item) use ($persalinan) {
            // $stat_persalinan = $persalinan->where('stat_rujuk_ibu' , $item['stat_rujuk_ibu'])->first();
            $stat_persalinan = $persalinan->where('stat_rujuk_ibu' , $item['stat_rujuk_ibu'])->count();                        
            // $label = 
            $stat = $item['stat_rujuk_ibu'] == 'c' ? 'Rujuk' : 'Normal';
            return [
                'label' => $stat,
                'count' => $stat_persalinan,                
            ];
        })->sortBy('name')->values();
        
        // scatter lila kehamilan
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

        #  immunisasi anak
        $detailImunisasi = ImunisasiDTL::with('header')
        ->whereHas('header', function ($query) use ($year) {
            $query->whereYear('tanggal_kunjungan', $year);
        })
        ->get()->map(function ($item) {
                // $detail = collect($item['header'])->pluck('tanggal_kujungan');
                // $imunDetail = array_column($detail, 'imunisasi');
                return [
                    'tanggal_kunjungan' => $item->header['tanggal_kunjungan'],
                    'imunisasi' => explode(' ', trim($item['imunisasi']))[0],
                ];
        })->values();

        $monthlyDataImunisasi = $detailImunisasi->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item['tanggal_kunjungan'])->format('m');
        })->map(function ($items, $month) {
            $imunisasiCounts = $items->groupBy('imunisasi')->map(function ($imunisasiItems, $imunisasi) {                

                return [
                    'label' => $imunisasi,
                    'count' => $imunisasiItems->count(),
                ];
            })->values()->toArray();
            
            $label = $this->month($month);
            
            return [
                'month' => $label,
                'data' => $imunisasiCounts,
            ];
        })->values()->toArray();
    

        // return $monthlyDataImunisasi;
        
        # persalinan
        $monthlyDataPersalinan = $persalinan->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item['tanggal_persalinan'])->format('m');
        })->map(function ($items, $month) {
            $persalinanCounts = $items->groupBy('stat_rujuk_ibu')->map(function ($persalinanItems, $persalinan) {
                if ($persalinan == 'c') {
                    $label = 'Rujuk';
                } else {
                    $label = 'Normal';
                }

                return [
                    'persalinan' => $label,
                    'count' => $persalinanItems->count(),
                ];
            })->values()->toArray();
            
            $label = $this->month($month);
            
            return [
                'month' => $label,
                'data' => $persalinanCounts,
            ];
        })->values()->toArray();

        // return $monthlyDataPersalinan;


        return view('welcome', [
            'kbData' => $kbData,
            'imunisasiData' => $imunisasiData,
            'kehamilanData' => $kehamilanData,
            'persalinanData' => $persalinanData,
            'rerata' => $rerata,
            'jumlah' => $jumlah,
            'countLayanan' => $countLayanan,
            'mostVacin' => $mostVacin,
            'mostKB' => $mostKB,
            'rerataMonth' => $rerataMonth,
            'yearList' => $yearList,
            'year' => $year,
            'chartImunisasi' => $mainImunisasi,
            'chartKB' => $chartKB,
            'mainPersalinan' => $mainPersalinan,
            'scatterLila' => $scatterLila,
            'KBdrill' => $monthlyDataKB,
            'Persalinandrill' => $monthlyDataPersalinan,
            'Imunisasidrill' => $monthlyDataImunisasi,

        ]);
    }

    public function dataCount($year, $data, $name)
    {
        $months = [1,2,3,4,5,6,7,8,9,10,11,12];
        foreach ($months as $month => $value) {

            $count = collect($data)->filter(function ($item) use ($value, $year) {

                if($item['tanggal_kunjungan'])
                {
                    $date = Carbon::parse($item['tanggal_kunjungan']);
                } else {
                    $date = Carbon::parse($item['tanggal_persalinan']);
                }
                return $date->year === $year && $date->month === $value;
            })->count();

            $label = $this->month($value);

            $Data[$month] = [
                'month' => $label,
                'count' => $count,
                'name' => $name                
            ];
        }
        return $Data;
    }

    public function month($month)
    {
        if((int)$month == 1) {
            return $label = 'Jan';
        } elseif ((int)$month == 2) {
            return $label = 'Feb';
        } elseif ((int)$month == 3) {
            return $label = 'Mar';
        } elseif ((int)$month == 4) {
            return $label = 'Apr';
        } elseif ((int)$month == 5) {
            return $label = 'May';
        } elseif ((int)$month == 6) {
            return $label = 'Jun';
        } elseif ((int)$month == 7) {
            return $label = 'Jul';
        } elseif ((int)$month == 8) {
            return $label = 'Aug';
        } elseif ((int)$month == 9) {
            return $label = 'Sep';
        } elseif ((int)$month == 10) {
            return $label = 'Oct';
        } elseif ((int)$month == 11) {
            return $label = 'Nov';
        } elseif ((int)$month == 12) {
            return $label = 'Des';
        }
    }
}
