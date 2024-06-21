{{-- @dd($dataAkseptor) --}}

@extends('layouts.main')
@extends('component.dateFilter')
@section('js')
    {{-- Pie Chart --}}
    {{-- <script>
        var akseptorData = @json($dataAkseptor);
        var dataLabels = ['MOW', 'IUD', 'Suntik', 'Pil', 'Kondom'];

        // Create an array of objects with 'name' and 'data' properties
        var updatedData = akseptorData.map((value, index) => {
            return {
                name: dataLabels[index],
                data: value
            };
        });

        // console.log(updatedData);
        var options = {
            series: akseptorData,
            chart: {
                width: 380,
                type: 'donut',
            },
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 270
                }
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'gradient',
            },
            legend: {
                formatter: function(val, opts) {
                    return val + " - " + opts.w.globals.series[opts.seriesIndex]
                }
            },
            title: {
                text: 'Akseptor KB'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#chartkb"), options);
        chart.render();
    </script> --}}

    {{-- Bar Chart --}}
    {{-- <script>
        var dataAkseptor = @json($dataAkseptor);

        var options = {
            series: [{
                name: 'Data Akseptor',
                data: dataAkseptor
            }],
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top', // top, center, bottom
                    },
                }
            },
            dataLabels: {
                enabled: true,
                // formatter: function(val) {
                //     return val + "%";
                // },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },

            xaxis: {
                categories: ["MOW", "IUD", "Suntik", "Pil", "Kondom"],
                position: 'top',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: false,
                    formatter: function(val) {
                        return val;
                    }
                }

            },
            title: {
                text: 'Data Akseptor',
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                    color: '#444'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();
    </script> --}}

    {{-- highchart --}}
    <script>
        // grafik jumlah penerima akseptor dan drilldown ya

        ///////////////////////////////// masukin data ke variable
        // data label
        const label = @json($label);
        // console.log(label);

        // data akseptor
        let aks = @json($data);
        // console.log(aks);

        /////////////////////// masukin data label dan akseptor ke format highchart

        // let resultArray = [];
        // for (let i = 0; i < label.length; i++) {
        //     resultArray.push({
        //         name: label[i].label,
        //         y: aks[i].count
        //     });
        // }

        const resultArray = label.map((item, index) => ({
            name: item.label,
            y: aks[index].count,
            drilldown: item.label.toLowerCase()
        }));
        // console.log(resultArray);

        // buat array drilldown

        //////////////// masukin data ke variable
        const dataDrilldown = @json($drilldown);
        // console.log(dataDrilldown);

        //////////////// masukin data drilldown ke format highchart drilldown

        // memasukkan data ke array sesuai dengan nama akseptor
        // contoh data : 
        // [{"akseptor":"iud","jenis":"KONTROL","count":6},
        // {"akseptor":"iud","jenis":"PASANG","count":2},
        // {"akseptor":"suntik","jenis":"1 BULAN","count":65},
        // {"akseptor":"suntik","jenis":"3 BULAN","count":27},
        // {"akseptor":"pil","jenis":"PIL","count":3}]


        const drillResult = Object.values(dataDrilldown.reduce((acc, cur) => {
            if (!acc[cur.akseptor]) {
                acc[cur.akseptor] = {
                    name: cur.akseptor.toUpperCase(),
                    id: cur.akseptor,
                    data: []
                };
            }
            acc[cur.akseptor].data.push([cur.jenis, cur.count]);
            return acc;
        }, {}));

        // console.log(drillResult);

        var header = "Hasil Pelayanan Kontrasepsi (KB)";

        // Create the chart
        Highcharts.chart('batang', {
            chart: {
                type: 'column'
            },
            title: {
                align: 'left',
                text: header
            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                align: 'left'
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                type: 'category',
                title: {
                    text: 'Jenis Kontrasepsi'
                }
            },
            yAxis: {
                title: {
                    text: 'Jumlah Pasien'
                }

            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> pasien<br/>'
            },

            series: [{
                name: 'Akseptor',
                colorByPoint: true,
                data: resultArray
            }],
            drilldown: {
                breadcrumbs: {
                    position: {
                        align: 'right'
                    }
                },
                series: drillResult
            }
        });
    </script>

    <script>
        const data = [{
                "browser": "Chrome",
                "version": "v65.0",
                "value": 0.1
            },
            {
                "browser": "Firefox",
                "version": "v66.0",
                "value": 0.3
            },
            {
                "browser": "Firefox",
                "version": "v67.0",
                "value": 0.2
            },
            {
                "browser": "Chrome",
                "version": "v69.0",
                "value": 0.9
            },
            // Add more objects as needed
        ];

        const result = Object.values(data.reduce((acc, cur) => {
            if (!acc[cur.browser]) {
                acc[cur.browser] = {
                    name: cur.browser,
                    id: cur.browser,
                    data: []
                };
            }
            acc[cur.browser].data.push([cur.version, cur.value]);
            return acc;
        }, {}));

        // console.log(result);
    </script>

    <script>
        //Grafik Perbandingan Riwayat Abortus
        // Data retrieved from https://netmarketshare.com/
        // Build the chart

        // masukkan data abortus dari controller ke variabel
        var abortus = @json($cAbortus);

        // console.log(abortus);

        // masukkan data ke format highchart
        const resultAbortus = abortus.map((item) => ({
            name: item.kategori,
            y: item.count,
        }));

        // console.log(resultAbortus);

        Highcharts.chart('abortus', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Riwayat Keguguran Pasien',
                align: 'left'
            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                align: 'left'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y} pasien  ({point.percentage:.1f}%)</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {y} pasien'
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                innerSize: '50%',
                data: resultAbortus
            }]
        });
    </script>

    <script>
        // Grafik Perbandingan Kunjungan Lama dan Baru
        // Data retrieved from https://netmarketshare.com/
        // Build the chart

        var kunjungan = @json($cKunjungan);

        // console.log(kunjungan);

        const resultKunjungan = kunjungan.map((item) => ({
            name: item.kunjungan,
            y: item.count,
        }));

        // console.log(resultKunjungan);

        Highcharts.chart('kunjungan', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Kunjugan Pasien',
                align: 'left'
            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                align: 'left'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y} pasien  ({point.percentage:.1f}%)</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {

                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {y} pasien',
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                innerSize: '50%',
                data: resultKunjungan
            }]
        });
    </script>

    {{-- GRAFIK USIA --}}
    <script>
        // masukkan data ke variable
        var mainAges = @json($mainAges);
        var drillAges = @json($drillAges);

        // console.log(mainAges);

        // menyesuaikan data dengan format grafik
        const rMainAges = mainAges.map(($item) => ({
            name: $item.label,
            y: $item.count,
            drilldown: $item.label
        }));

        console.log(rMainAges);

        const rDrillAges = Object.values(drillAges.reduce((acc, cur) => {
            if (!acc[cur.label]) {
                acc[cur.label] = {
                    name: cur.label,
                    id: cur.label,
                    data: []
                };
            }
            acc[cur.label].data.push([cur.umur.toString(), cur.count]);
            return acc;
        }, {}));

        // console.log(rDrillAges);

        // Create the chart
        Highcharts.chart('usia', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Rentang Umur Ibu KB',
                align: 'left'
            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                align: 'left'
            },

            accessibility: {
                announceNewData: {
                    enabled: true
                },
                point: {
                    valueSuffix: '%'
                }
            },

            plotOptions: {
                series: {
                    showInLegend: true,
                    borderRadius: 5,
                    dataLabels: {
                        enabled: true,
                        format: 'Umur {point.name}: {point.percentage:.1f}%'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">Umur {point.name}</span>: <b>{point.y}</b> of total<br/>'
            },

            series: [{
                name: 'Rentang Umur Ibu',
                colorByPoint: true,
                innerSize: '50%',
                data: rMainAges
            }],
            drilldown: {
                innerSize: '50%',
                series: rDrillAges
            }
        });
    </script>
@endsection

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <div class="card shadow">
            <div class="card-body row">
                <div class="col">
                    <form action="{{ route('kb.index') }}" method="GET">
                        @csrf
                        @method('POST')
                        <label for="datepicker">Mulai Tanggal:</label>
                        <input type="date" class="form-control" placeholder="Pilih tanggal" name="awal"
                            value="{{ $awal }}">
                </div>
                <div class="col">

                    <label for="datepicker">Sampai Tanggal:</label>
                    <input type="date" class="form-control" placeholder="Pilih tanggal" name="akhir"
                        value="{{ $akhir }}">
                </div>
                <div class="align-items-center d-flex pt-3">

                    <button class="btn btn-info">Tampilkan</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Pesan Sukses --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Pesan Error --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- -------------------------------------------- SCOREBOARD --------------------------------------- --}}
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Kunjungan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kunjungan }} Pasien</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                            {{-- <i class="fas fa-calendar fa-2x text-gray-300"></i> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Rerata Umur Ibu KB</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rerata }} Tahun</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-female fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Ibu KB
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $jumlah_ibu }} Orang</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kontrasepsi Terbanyak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mostKB }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-prescription-bottle-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END SCOREBOARD --------------------------------------- --}}


    {{-- -------------------------------------------- DIAGRAM 1 ------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagram
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="batang"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagram
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="kunjungan" class="kunjungan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END DIAGRAM 1 ------------------------------------------------ --}}

    {{-- DIAGRAM 2 --}}
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagram
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="abortus" class="abortus"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagram
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="usia" class="usia"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <div class="card shadow mb-4">
        <div class="card-body d-flex">
            <!-- Button trigger modal -->
            <div class="p-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Import
                </button>
            </div>

            {{-- <div class="p-2">
                <a href="/export-excel-kb">
                    <button type="button" class="btn btn-success">
                        Export
                    </button>
                </a>
            </div> --}}
        </div>
    </div>



    <!-- Modal Dialog Import -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/import-excel-kb" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <label for="exampleFormControlFile1">Excel file</label>
                        <input type="file" name="file" class="form-control-file" id="exampleFormControlFile1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Dialog Import -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4 d-none">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal Kunjungan</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jumlah Anak</th>
                            <th>Akseptor</th>
                            <th>MOW</th>
                            <th>IUD</th>
                            <th>Suntik</th>
                            <th>Pil</th>
                            <th>Kondom</th>
                            <th>Kunjungan</th>
                        </tr>
                    </thead>
                    {{-- <tfoot>
                        <tr>
                            <th>Tanggal Kunjungan</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jumlah Anak</th>
                            <th>Akseptor</th>
                            <th>MOW</th>
                            <th>IUD</th>
                            <th>Suntik</th>
                            <th>Pil</th>
                            <th>Kondom</th>
                            <th>Kunjungan</th>
                        </tr>
                    </tfoot> --}}
                    <tbody>
                        @foreach ($KB as $dataKB)
                            <tr>
                                <td>{{ $dataKB->tanggal_kunjungan }}</td>
                                <td>{{ $dataKB->nik }}</td>
                                @foreach ($dataKB->ibu as $dataIbu)
                                    <td>{{ $dataIbu->nama }}</td>
                                @endforeach
                                <td>{{ $dataKB->jumlah_anak }}</td>
                                <td class="text-center">
                                    @if ($dataKB->akseptor == 1)
                                        MOW
                                    @elseif ($dataKB->akseptor == 2)
                                        IUD
                                    @elseif ($dataKB->akseptor == 3)
                                        Suntik
                                    @elseif ($dataKB->akseptor == 4)
                                        Pil
                                    @elseif ($dataKB->akseptor == 5)
                                        Kondom
                                    @endif
                                </td>
                                <td>{{ $dataKB->mow }}</td>
                                <td>{{ $dataKB->iud }}</td>
                                <td>{{ $dataKB->suntik }}</td>
                                <td>{{ $dataKB->pil }}</td>
                                <td>{{ $dataKB->kondom }}</td>
                                <td class="text-center">
                                    @if ($dataKB->kunjungan == 'l')
                                        Lama
                                    @elseif ($dataKB->kunjungan == 'b')
                                        Baru
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
