{{-- @dd($dataAkseptor) --}}

@extends('layouts.main')
@extends('component.dateFilter')
@section('js')
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

        // console.log(rMainAges);

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
                text: 'Rentang Umur Ibu Hamil',
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

    {{-- GRAFIK RESTI --}}
    <script>
        // masukkan data ke variable
        var mainResti = @json($mainResti);
        var drillResti = @json($drillResti);

        console.log(mainResti);

        // menyesuaikan data dengan format grafik
        const rmainResti = mainResti.map(($item) => ({
            name: $item.status,
            y: $item.count,
            drilldown: $item.status === 'Ada Resiko' ? $item.status : null
        }));

        console.log(rmainResti);

        const rDrillResti = Object.values(drillResti.reduce((acc, cur) => {
            if (!acc[cur.status]) {
                acc[cur.status] = {
                    resti: cur.resti,
                    name: cur.status,
                    id: cur.status,
                    data: []
                };
            }
            acc[cur.status].data.push([cur.resti, cur.count]);
            return acc;
        }, {}));

        console.log(rDrillResti);

        // Create the chart
        Highcharts.chart('resti', {
            title: {
                text: 'Resiko Tinggi Ibu Hamil',
                align: 'left'
            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                align: 'left'
            },
            xAxis: {
                type: 'category', // Set the x-axis type to category
                labels: {
                    enabled: false // Hide the x-axis labels
                }
            },
            yAxis: {
                visible: false // Hide the y-axis
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
                        format: '{point.name}: {point.y} Pasien'
                    },

                }
            },

            tooltip: {

                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> ({point.percentage:.1f}%)<br/>'
            },

            series: [{
                name: 'Resiko Tinggi Ibu Hamil',
                type: 'pie',
                colorByPoint: true,
                innerSize: '50%',
                data: rmainResti
            }],
            drilldown: {
                innerSize: '50%',
                series: rDrillResti.map(drillSeries => {
                    return {
                        type: 'bar', // Specify the type as 'column'
                        name: drillSeries.resti,
                        id: drillSeries.name,
                        data: drillSeries.data,
                        tooltip: {
                            headerFormat: '<span style="font-size:11px">{point.resti}</span><br>',
                            pointFormat: '<b>{point.name}</b>: {point.y} Ibu'
                        }
                    };
                })
            }
        });
    </script>



    {{-- GRAFIK LILA --}}
    <script>
        // masukkan data ke variable
        var mainLila = @json($mainLila);
        var drillLila = @json($drillLila);

        // console.log(mainLila);

        // menyesuaikan data dengan format grafik
        const rmainLila = mainLila.map(($item) => ({
            name: $item.status,
            y: $item.count,
            drilldown: $item.status
        }));

        // console.log(rmainLila);

        const rDrillLila = Object.values(drillLila.reduce((acc, cur) => {
            if (!acc[cur.status]) {
                acc[cur.status] = {
                    name: cur.status,
                    id: cur.status,
                    data: []
                };
            }
            acc[cur.status].data.push([cur.lila, cur.count]);
            return acc;
        }, {}));

        // console.log(rDrillLila);

        // Create the chart
        Highcharts.chart('lila', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Lingkar Lengan Atas Ibu Hamil',
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
                        format: '{point.name}: {point.y} Pasien'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> ({point.percentage:.1f}%)<br/>'
            },

            series: [{
                name: 'LILA Ibu Hamil',
                colorByPoint: true,
                innerSize: '50%',
                data: rmainLila
            }],
            drilldown: {
                innerSize: '50%',
                series: rDrillLila
            }
        });
    </script>

    <script>
        $(function() {

            Highcharts.chart('container', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Drilldown labels and legends'
                },
                xAxis: {
                    type: 'category'
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                        }
                    }
                },
                series: [{
                    name: 'Things',
                    colorByPoint: true,
                    data: [{
                        name: 'Animals',
                        y: 5,
                        drilldown: 'animals'
                    }, {
                        name: 'Fruits',
                        y: 2,
                        drilldown: 'fruits'
                    }, {
                        name: 'Cars',
                        y: 4,
                        drilldown: 'cars'
                    }]
                }],
                drilldown: {
                    series: [{
                        id: 'animals',
                        data: [
                            ['Cats', 4],
                            ['Dogs', 2],
                            ['Cows', 1],
                            ['Sheep', 2],
                            ['Pigs', 1]
                        ]
                    }, {
                        id: 'fruits',
                        data: [
                            ['Apples', 4],
                            ['Oranges', 2]
                        ]
                    }, {
                        id: 'cars',
                        data: [
                            ['Toyota', 4],
                            ['Opel', 2],
                            ['Volkswagen', 2]
                        ]
                    }],
                    drillUpButton: {
                        relativeTo: 'spacingBox',
                        position: {
                            y: 0,
                            x: 0
                        }
                    }
                }
            });
        });
    </script>

    <script>
        var scatterData = @json($scatterLila);
        console.log(scatterData);

        Highcharts.chart('scatterLila', {
            chart: {
                type: 'scatter',
                zoomType: 'xy'
            },
            title: {
                text: 'Sebaran BB dan LILA Ibu Hamil'

            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                // align: 'left'
            },
            xAxis: {
                title: {
                    enabled: true,
                    text: 'Berat Badan (Kg)'
                }
            },
            yAxis: {
                title: {
                    text: 'Lingkar Lengan Atas (cm)'
                },
                plotLines: [{
                    color: 'red',
                    dashStyle: 'solid',
                    value: 23.5,
                    width: 2,
                    label: {
                        text: 'Batasan LILA (23.5 cm)',
                        align: 'right',
                        style: {
                            color: 'gray'
                        }
                    }
                }]
            },
            tooltip: {
                pointFormat: 'Berat: {point.x} Kg <br/> Panjang: {point.y} cm'
            },
            series: [{
                name: 'Ibu Hamil',
                color: 'rgba(223, 83, 83, .5)',
                data: scatterData
            }]
        });
    </script>

    <script>
        // Custom template helper
        // Highcharts.Templating.helpers.abs = value => Math.abs(value);

        // kategori umur
        var rangeUmur = @json($rangeUmur);
        console.log(rangeUmur);
        var gpa = @json($dataGPA);

        console.log(gpa);


        Highcharts.chart('pyramid', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Primigravida dan Multigravida berdasarkan Kelompok Umur',
                align: 'left'
            },
            subtitle: {
                text: dateFrom + " - " + dateTo,
                align: 'left'
            },
            xAxis: {
                categories: rangeUmur,
                title: {
                    text: 'Kelompok Umur'
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Ibu Hamil',
                },
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: '  Orang'
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: 80,
                floating: true,
                borderWidth: 1,
                backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: gpa
        });
    </script>
@endsection

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <div class="card shadow">
            <div class="card-body row">
                <div class="col">
                    <form action="{{ route('kehamilan.index') }}" method="GET">
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
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Kunjungan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kunjungan }} Pasien</div>
                            {{-- <div class="h5 mb-0 font-weight-bold text-gray-800"></div> --}}
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
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Jumlah Ibu Hamil</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ibuHamil }} Orang</div>

                        </div>
                        <div class="col-auto">
                            <i class="fas fa-female fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rerata Ibu Hamil
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $rerata }} Tahun</div>
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
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END SCOREBOARD --------------------------------------- --}}


    {{-- ----------------------------------------------- DIAGRAM 1 -------------------------------------------- --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">RESTI
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="resti" class="resti"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sebaran Umur
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
    {{-- --------------------------------------------------------DIAGRAM2------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">LILA
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="scatterLila"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4 d-none">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">GPA
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="container" class="container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- --------------------------------------------------------DIAGRAM3------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagram
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="pyramid"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4 d-none">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">GPA
                    </h6>
                </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="" class=""></div>
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
                <form action="{{ route('kehamilan.import') }}" method="POST" enctype="multipart/form-data">
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
                            <th>Umur</th>
                            <th>Keluhan</th>
                            <th>GPA</th>
                            <th>Grav (minggu)</th>
                            <th>Berat Badan (Kg)</th>
                            <th>Tinggi Badan (cm)</th>
                            <th>Tekanan Darah (mmHg)</th>
                            <th>LILA (cm)</th>
                            <th>Resiko Tinggi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kehamilan as $dataKehamilan)
                            <tr>
                                <td>{{ $dataKehamilan->tanggal_kunjungan }}</td>
                                <td>{{ $dataKehamilan->nik }}</td>
                                @foreach ($dataKehamilan->ibu as $dataIbu)
                                    <td>{{ $dataIbu->nama }}</td>
                                @endforeach
                                {{-- <td>{{ $dataKehamilan->anak->nama_bayi }}</td> --}}
                                <td>{{ $dataKehamilan->umur }}</td>
                                <td>{{ $dataKehamilan->keluhan }}</td>
                                <td>{{ $dataKehamilan->gpa }}</td>
                                <td>{{ $dataKehamilan->gravida }}</td>
                                <td>{{ $dataKehamilan->bb }}</td>
                                <td>{{ $dataKehamilan->tb }}</td>
                                <td>{{ $dataKehamilan->tekanan_darah }}</td>
                                <td>{{ $dataKehamilan->lila }}</td>
                                <td>{{ $dataKehamilan->resti }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
