{{-- @dd($dataAkseptor) --}}

@extends('layouts.main')
@extends('component.dateFilter')
@section('js')
    {{-- highchart --}}
    <script>
        // grafik jumlah penerima imunisasi dan drilldown nya

        // masukkan main data dan drilldown data ke variable
        var mainImunisasi = @json($mainImunisasi);
        var drillImunisasi = @json($drillImunisasi);
        // console.log(mainImunisasi);

        // masukan data mainImunisasi dan drillImunisasi ke format highchart
        // format mainImunisasi
        const resultMain = mainImunisasi.map((item, index) => ({
            name: item.label,
            y: item.count,
            drilldown: item.label
        }));

        console.log(resultMain);

        // format drillImunisasi
        const drillResult = Object.values(drillImunisasi.reduce((acc, cur) => {
            if (!acc[cur.label]) {
                acc[cur.label] = {
                    name: cur.label,
                    id: cur.label,
                    data: []
                };
            }
            acc[cur.label].data.push([cur.imunisasi, cur.count]);
            return acc;
        }, {}));

        // var dateFrom = @json($awal);
        console.log(drillResult);

        // Create the chart
        Highcharts.chart('batang', {
            chart: {
                type: 'column'
            },
            title: {
                align: 'left',
                text: "Imunisasi"
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
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Jumlah Anak'
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
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> anak<br/>'
            },

            series: [{
                name: 'Akseptor',
                colorByPoint: true,
                data: resultMain
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

    {{-- GRAFIK USIA --}}
    <script>
        // masukkan data ke variable
        var mainPlace = @json($mainTempatLahir);
        var drillPlace = @json($drillTempatLahir);

        // console.log(mainPlace);

        // menyesuaikan data dengan format grafik
        const rMainPlace = mainPlace.map(($item) => ({
            name: $item.tempat_lahir,
            y: $item.count,
            drilldown: $item.tempat_lahir
        }));

        console.log(rMainPlace);

        const rDrillPlace = Object.values(drillPlace.reduce((acc, cur) => {
            if (!acc[cur.tempat_lahir]) {
                acc[cur.tempat_lahir] = {
                    name: cur.tempat_lahir,
                    id: cur.tempat_lahir,
                    data: []
                };
            }
            acc[cur.tempat_lahir].data.push([cur.jenis_kelamin, cur.count]);
            return acc;
        }, {}));

        // console.log(rDrillPlace);

        // Create the chart
        Highcharts.chart('tempatLahir', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Tempat Kelahiran Bayi',
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
                        format: '{point.name}:<br>{y} Bayi'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                // pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b>  ({point.percentage:.1f}%)<br/>'
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b>  ({point.percentage:.1f}%)<br/>'
            },

            series: [{
                name: 'Tempat Lahir Bayi',
                colorByPoint: true,
                innerSize: '50%',
                data: rMainPlace
            }],
            drilldown: {
                innerSize: '50%',
                series: rDrillPlace
            }
        });
    </script>

    <script>
        var scatterData = @json($scatterData)

        // console.log(scatterData);
        Highcharts.chart('bbPerUmur', {
            chart: {
                type: 'scatter',
                zoomType: 'xy'
            },
            title: {
                text: 'Sebaran BB per Umur'
            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                align: 'center'
            },
            xAxis: {
                title: {
                    enabled: true,
                    text: 'Umur (Bulan)'
                }
            },
            yAxis: {
                title: {
                    text: 'Berat Badan (Kg)'
                }
            },
            tooltip: {
                pointFormat: 'Nama:{point.nama_bayi}<br>Umur: {point.x} Bulan <br/> Berat: {point.y} Kg'
            },
            series: [{
                name: 'Bayi',
                color: 'rgba(223, 83, 83, .5)',
                data: scatterData
            }]
        });
    </script>
@endsection

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <div class="card shadow">
            <div class="card-body row">
                <div class="col">
                    <form action="{{ route('imunisasi.index') }}" method="GET">
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

    @if ($errors->any())
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- -------------------------------------------- SCOREBOARD --------------------------------------- --}}
    {{-- <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <label for="datepicker">Mulai Tanggal:</label>
                            <input type="text" class="form-control datepicker" name="datepicker">
                        </div>
                        <div class="col mr-2">
                            <label for="datepicker">Sampai Tanggal:</label>
                            <input type="text" class="form-control datepicker" name="datepicker">
                        </div>
                        <div>

                            <button type="button" class="btn btn-info">Go</button>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">50%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%"
                                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
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
                                Pending Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- -------------------------------------------- END SCOREBOARD --------------------------------------- --}}

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
                                Imunisasi Terbanyak</div>
                            @if ($mostVacin)
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mostVacin['label'] }}</div>
                            @else
                                <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crutch fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Anak Laki laki
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $laki }} Anak</div>
                                </div>
                                {{-- <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%"
                                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mars fa-2x text-gray-300"></i>
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
                                Jumlah Anak Perempuan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $perempuan }} Anak</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-venus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END SCOREBOARD --------------------------------------- --}}


    {{-- CARD DIAGRAM 1 --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jenis Imunisasi
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
                    <h6 class="m-0 font-weight-bold text-primary">tempatLahir
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="tempatLahir" class="tempatLahir"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD DIAGRAM 2 --}}
    <div class="row">
        <div class="col-lg-4 mb-4 d-none">
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

        <div class="col mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">BB per Umur
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="bbPerUmur" class="bbPerUmur"></div>
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
                <form action="{{ route('imunisasi.import') }}" method="POST" enctype="multipart/form-data">
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
                            <th>Nama Ibu</th>
                            <th>Nama Anak</th>
                            <th>Berat Badan</th>
                            <th>Umur</th>
                            <th>Jumlah Imunisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($header as $dataHeader)
                            <tr>
                                <td>{{ $dataHeader->tanggal_kunjungan }}</td>
                                {{-- <td>{{ $dataHeader->nik }}</td> --}}
                                @foreach ($dataHeader->ibu as $dataIbu)
                                    <td>{{ $dataIbu->nama }}</td>
                                @endforeach
                                {{-- @foreach ($dataHeader->anak as $anak)
                                    <td>{{ $anak->nama_bayi }}</td>
                                    @endforeach --}}
                                <td>{{ $dataHeader->anak->nama_bayi }}</td>
                                <td>{{ $dataHeader->bb }}</td>
                                <td>{{ $dataHeader->umur }}</td>
                                <td>
                                    {{-- {{ $dataHeader->detail->count() }} --}}
                                    @foreach ($dataHeader->detail->sortBy('imunisasi') as $detail)
                                        <li>{{ $detail->imunisasi }}</li>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
