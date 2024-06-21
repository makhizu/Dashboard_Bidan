{{-- @dd($labelJK) --}}
@extends('layouts.main')

@section('js')
    <script>
        // let JK = @json($JK);

        // alert(JK);
        var options = {
            series: @json($JK),
            labels: @json($labelJK),
            chart: {
                width: 450,
                type: 'donut',
            },
            title: {
                text: 'Persentase Jenis Kelamin Anak'
            },
            responsive: [{
                breakpoint: 400,
                options: {
                    chart: {
                        width: 100
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#JK"), options);
        chart.render();
    </script>

    <script>
        var options = {
            series: @json($AnakLahir),
            labels: @json($TempatLahir),
            chart: {
                width: 450,
                type: 'donut',
            },
            title: {
                text: 'Persentase Tempat Lahir Anak'
            },
            responsive: [{
                breakpoint: 400,
                options: {
                    chart: {
                        width: 100
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#tempatlahir"), options);
        chart.render();
    </script>

    <script>
        var nested = @json($nestedLahir);
        //array tampung
        var seriesData = [];
        for (var JK in nested) {
            if (nested.hasOwnProperty(JK)) {
                seriesData.push({
                    name: JK,
                    data: [nested[JK]['RS'], nested[JK]['PMB']]
                });
            }
        }

        var options = {
            series: seriesData,
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($TempatLahir),
                title: {
                    text: 'Perbandingan tempat lahir'
                }
            },
            yaxis: {
                title: {
                    text: 'jumlah anak'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " anak"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#JKLahir"), options);
        chart.render();
    </script>

    <script>
        // Data retrieved from https://netmarketshare.com
        Highcharts.chart('bulet', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Jumlah Anak',
                align: 'center'
            },
            tooltip: {
                pointFormat: '<b>{point.percentage:.1f}</b> Anak'
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
                        format: '<b>{point.name}</b>: {point.percentage:.1f} '
                    }
                }
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                data: [{
                    name: 'Safari',
                    y: 2.63
                }, {
                    name: 'Internet Explorer',
                    y: 1.53
                }, {
                    name: 'Opera',
                    y: 1.40
                }]
            }]
        });
    </script>

    <script>
        // Create the chart

        let lahir = @json($result)

        // const string = JSON.stringify(lahir);
        // document.querySelector("#print").innerHTML = string;

        var formattedData = [];

        var formattedItem = [];

        lahir.forEach(item => {
            formattedItem = {
                name: item.jenis_kelamin,
                y: item.count
            };
            formattedData.push(formattedItem);
        });

        Highcharts.chart('container', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Perbandingan Jumlah Anak',
                align: 'left'
            },
            subtitle: {

                align: 'left'
            },

            accessibility: {
                announceNewData: {
                    enabled: true
                },
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Jumlah Bayi'
                }
            },
            plotOptions: {
                series: {
                    borderRadius: 5,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
            },

            series: [{
                name: 'Jenis Kelamin',
                colorByPoint: true,
                data: formattedData
            }],
            // drilldown: {
            //     series: [{
            //             name: 'Chrome',
            //             id: 'Chrome',
            //             data: [
            //                 [
            //                     'v97.0',
            //                     36.89
            //                 ],
            //                 [
            //                     'v96.0',
            //                     18.16
            //                 ],
            //                 [
            //                     'v95.0',
            //                     0.54
            //                 ],
            //                 [
            //                     'v94.0',
            //                     0.7
            //                 ],
            //                 [
            //                     'v93.0',
            //                     0.8
            //                 ],
            //                 [
            //                     'v92.0',
            //                     0.41
            //                 ],
            //                 [
            //                     'v91.0',
            //                     0.31
            //                 ],
            //                 [
            //                     'v90.0',
            //                     0.13
            //                 ],
            //                 [
            //                     'v89.0',
            //                     0.14
            //                 ],
            //                 [
            //                     'v88.0',
            //                     0.1
            //                 ],
            //                 [
            //                     'v87.0',
            //                     0.35
            //                 ],
            //                 [
            //                     'v86.0',
            //                     0.17
            //                 ],
            //                 [
            //                     'v85.0',
            //                     0.18
            //                 ],
            //                 [
            //                     'v84.0',
            //                     0.17
            //                 ],
            //                 [
            //                     'v83.0',
            //                     0.21
            //                 ],
            //                 [
            //                     'v81.0',
            //                     0.1
            //                 ],
            //                 [
            //                     'v80.0',
            //                     0.16
            //                 ],
            //                 [
            //                     'v79.0',
            //                     0.43
            //                 ],
            //                 [
            //                     'v78.0',
            //                     0.11
            //                 ],
            //                 [
            //                     'v76.0',
            //                     0.16
            //                 ],
            //                 [
            //                     'v75.0',
            //                     0.15
            //                 ],
            //                 [
            //                     'v72.0',
            //                     0.14
            //                 ],
            //                 [
            //                     'v70.0',
            //                     0.11
            //                 ],
            //                 [
            //                     'v69.0',
            //                     0.13
            //                 ],
            //                 [
            //                     'v56.0',
            //                     0.12
            //                 ],
            //                 [
            //                     'v49.0',
            //                     0.17
            //                 ]
            //             ]
            //         },
            //         {
            //             name: 'Safari',
            //             id: 'Safari',
            //             data: [
            //                 [
            //                     'v15.3',
            //                     0.1
            //                 ],
            //                 [
            //                     'v15.2',
            //                     2.01
            //                 ],
            //                 [
            //                     'v15.1',
            //                     2.29
            //                 ],
            //                 [
            //                     'v15.0',
            //                     0.49
            //                 ],
            //                 [
            //                     'v14.1',
            //                     2.48
            //                 ],
            //                 [
            //                     'v14.0',
            //                     0.64
            //                 ],
            //                 [
            //                     'v13.1',
            //                     1.17
            //                 ],
            //                 [
            //                     'v13.0',
            //                     0.13
            //                 ],
            //                 [
            //                     'v12.1',
            //                     0.16
            //                 ]
            //             ]
            //         },
            //         {
            //             name: 'Edge',
            //             id: 'Edge',
            //             data: [
            //                 [
            //                     'v97',
            //                     6.62
            //                 ],
            //                 [
            //                     'v96',
            //                     2.55
            //                 ],
            //                 [
            //                     'v95',
            //                     0.15
            //                 ]
            //             ]
            //         },
            //         {
            //             name: 'Firefox',
            //             id: 'Firefox',
            //             data: [
            //                 [
            //                     'v96.0',
            //                     4.17
            //                 ],
            //                 [
            //                     'v95.0',
            //                     3.33
            //                 ],
            //                 [
            //                     'v94.0',
            //                     0.11
            //                 ],
            //                 [
            //                     'v91.0',
            //                     0.23
            //                 ],
            //                 [
            //                     'v78.0',
            //                     0.16
            //                 ],
            //                 [
            //                     'v52.0',
            //                     0.15
            //                 ]
            //             ]
            //         }
            //     ]
            // }
        });
    </script>

    {{-- id = browser --}}
    <script>
        // Create the chart
        Highcharts.chart('browser', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Perbandingan Jumlah Anak',
                align: 'left'
            },
            subtitle: {
                text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>',
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
                    borderRadius: 5,
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y:.1f}%'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
            },

            series: [{
                name: 'Browsers',
                colorByPoint: true,
                data: [{
                        name: 'Chrome',
                        y: 61.04,
                        drilldown: 'Chrome'
                    },
                    {
                        name: 'Safari',
                        y: 9.47,
                        drilldown: 'Safari'
                    },
                    {
                        name: 'Edge',
                        y: 9.32,
                        drilldown: 'Edge'
                    },
                    {
                        name: 'Firefox',
                        y: 8.15,
                        drilldown: 'Firefox'
                    },
                    {
                        name: 'Other',
                        y: 11.02,
                        drilldown: null
                    }
                ]
            }],
            drilldown: {
                series: [{
                        name: 'Chrome',
                        id: 'Chrome',
                        data: [
                            [
                                'v97.0',
                                36.89
                            ],
                            [
                                'v96.0',
                                18.16
                            ],
                            [
                                'v95.0',
                                0.54
                            ],
                            [
                                'v94.0',
                                0.7
                            ],
                            [
                                'v93.0',
                                0.8
                            ],
                            [
                                'v92.0',
                                0.41
                            ],
                            [
                                'v91.0',
                                0.31
                            ],
                            [
                                'v90.0',
                                0.13
                            ],
                            [
                                'v89.0',
                                0.14
                            ],
                            [
                                'v88.0',
                                0.1
                            ],
                            [
                                'v87.0',
                                0.35
                            ],
                            [
                                'v86.0',
                                0.17
                            ],
                            [
                                'v85.0',
                                0.18
                            ],
                            [
                                'v84.0',
                                0.17
                            ],
                            [
                                'v83.0',
                                0.21
                            ],
                            [
                                'v81.0',
                                0.1
                            ],
                            [
                                'v80.0',
                                0.16
                            ],
                            [
                                'v79.0',
                                0.43
                            ],
                            [
                                'v78.0',
                                0.11
                            ],
                            [
                                'v76.0',
                                0.16
                            ],
                            [
                                'v75.0',
                                0.15
                            ],
                            [
                                'v72.0',
                                0.14
                            ],
                            [
                                'v70.0',
                                0.11
                            ],
                            [
                                'v69.0',
                                0.13
                            ],
                            [
                                'v56.0',
                                0.12
                            ],
                            [
                                'v49.0',
                                0.17
                            ]
                        ]
                    },
                    {
                        name: 'Safari',
                        id: 'Safari',
                        data: [
                            [
                                'v15.3',
                                0.1
                            ],
                            [
                                'v15.2',
                                2.01
                            ],
                            [
                                'v15.1',
                                2.29
                            ],
                            [
                                'v15.0',
                                0.49
                            ],
                            [
                                'v14.1',
                                2.48
                            ],
                            [
                                'v14.0',
                                0.64
                            ],
                            [
                                'v13.1',
                                1.17
                            ],
                            [
                                'v13.0',
                                0.13
                            ],
                            [
                                'v12.1',
                                0.16
                            ]
                        ]
                    },
                    {
                        name: 'Edge',
                        id: 'Edge',
                        data: [
                            [
                                'v97',
                                6.62
                            ],
                            [
                                'v96',
                                2.55
                            ],
                            [
                                'v95',
                                0.15
                            ]
                        ]
                    },
                    {
                        name: 'Firefox',
                        id: 'Firefox',
                        data: [
                            [
                                'v96.0',
                                4.17
                            ],
                            [
                                'v95.0',
                                3.33
                            ],
                            [
                                'v94.0',
                                0.11
                            ],
                            [
                                'v91.0',
                                0.23
                            ],
                            [
                                'v78.0',
                                0.16
                            ],
                            [
                                'v52.0',
                                0.15
                            ]
                        ]
                    }
                ]
            }
        });
    </script>
    <script>
        Highcharts.setOptions({
            colors: ['rgba(5,141,199,0.5)', 'rgba(80,180,50,0.5)', 'rgba(237,86,27,0.5)']
        });

        const series = [{
                name: 'Basketball',
                id: 'basketball',
                marker: {
                    symbol: 'circle'
                }
            },
            {
                name: 'Triathlon',
                id: 'triathlon',
                marker: {
                    symbol: 'triangle'
                }
            },
            {
                name: 'Volleyball',
                id: 'volleyball',
                marker: {
                    symbol: 'square'
                }
            }
        ];


        async function getData() {
            const response = await fetch(
                'https://cdn.jsdelivr.net/gh/highcharts/highcharts@24912efc85/samples/data/olympic2012.json'
            );
            return response.json();
        }


        getData().then(data => {
            const getData = sportName => {
                const temp = [];
                data.forEach(elm => {
                    if (elm.sport === sportName && elm.weight > 0 && elm.height > 0) {
                        temp.push([elm.height, elm.weight]);
                    }
                });
                return temp;
            };
            series.forEach(s => {
                s.data = getData(s.id);
            });

            console.log(series)

            Highcharts.chart('beratpanjang', {
                chart: {
                    type: 'scatter',
                    zoomType: 'xy'
                },
                title: {
                    text: 'Olympics athletes by height and weight',
                    align: 'left'
                },
                subtitle: {
                    text: 'Source: <a href="https://www.theguardian.com/sport/datablog/2012/aug/07/olympics-2012-athletes-age-weight-height">The Guardian</a>',
                    align: 'left'
                },
                xAxis: {
                    title: {
                        text: 'Height'
                    },
                    labels: {
                        format: '{value} m'
                    },
                    startOnTick: true,
                    endOnTick: true,
                    showLastLabel: true
                },
                yAxis: {
                    title: {
                        text: 'Weight'
                    },
                    labels: {
                        format: '{value} kg'
                    }
                },
                legend: {
                    enabled: true
                },
                plotOptions: {
                    scatter: {
                        marker: {
                            radius: 2.5,
                            symbol: 'circle',
                            states: {
                                hover: {
                                    enabled: true,
                                    lineColor: 'rgb(100,100,100)'
                                }
                            }
                        },
                        states: {
                            hover: {
                                marker: {
                                    enabled: false
                                }
                            }
                        },
                        jitter: {
                            x: 0.005
                        }
                    }
                },
                tooltip: {
                    pointFormat: 'Height: {point.x} m <br/> Weight: {point.y} kg'
                },
                series
            });
        });
    </script>

    <script>
        var scatterData = @json($scatter)

        console.log(scatterData);
        Highcharts.chart('beratpanjanganak', {
            chart: {
                type: 'scatter',
                zoomType: 'xy'
            },
            title: {
                text: 'Sebaran BB dan PB Lahir Bayi'
            },
            xAxis: {
                title: {
                    enabled: true,
                    text: 'Berat Badan (g)'
                }
            },
            yAxis: {
                title: {
                    text: 'Panjang Badan (cm)'
                }
            },
            tooltip: {
                pointFormat: 'Berat: {point.x} g <br/> Panjang: {point.y} cm'
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
    {{-- <h1 class="h3 mb-2 text-gray-800">Tables</h1>
    <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
        For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official
            DataTables documentation</a>.</p> --}}
    <div class="card shadow mb-4">
        <div class="card-body d-flex">
            <!-- Button trigger modal -->
            <div class="p-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Import
                </button>
            </div>

            {{-- <div class="p-2">
                <a href="/export-excel-anak">
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
                <form action="/import-excel-anak" method="POST" enctype="multipart/form-data">
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

    {{-- Pesan Sukses --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{-- Data anak <strong>Berhasil</strong> diimport. --}}
            {!! session('success') !!}
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



    {{-- <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Diagram
                </h6>
            </div>
            {{-- <div id="bulet">

            </div> 
    <figure class="highcharts-figure">
        <div id="container"></div>
    </figure>
    </div>
    </div> --}}

    {{-- <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Diagram
                </h6>
            </div>
            <figure class="highcharts-figure">
                <div id="beratpanjang"></div>
                <p class="highcharts-description">
                    Pie chart where the individual slices can be clicked to expose more
                    detailed data.
                </p>
            </figure>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            {{-- <div class="col mb-4"> --}}

            <div class="card shadow  ">
                <div class="card-body p-0">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Diagram
                        </h6>
                    </div>
                    {{-- <div id="bulet">

            </div> --}}
                    <figure class="highcharts-figure">
                        <div id="beratpanjanganak"></div>
                        {{-- <p class="highcharts-description">
                    Pie chart where the individual slices can be clicked to expose more
                    detailed data.
                </p> --}}
                    </figure>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">

            <div class="card shadow  ">
                <div class="card-body p-0">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Diagram
                        </h6>
                    </div>
                    {{-- <div id="bulet">
        
                    </div> --}}
                    <figure class="highcharts-figure">
                        <div id="container"></div>
                    </figure>
                </div>
            </div>
        </div>

    </div>





    {{-- <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Diagram
            </h6>
        </div>
        <div class="card-body">
            <div id="demo" class="carousel slide" data-ride="carousel">

                <!-- Indicators -->
                <ul class="carousel-indicators mb-0">
                    <li data-target="#demo" data-slide-to="0" class="active" style="background-color: navy"></li>
                    <li data-target="#demo" data-slide-to="1" style="background-color: navy"></li>
                    <li data-target="#demo" data-slide-to="2" style="background-color: navy"></li>
                </ul>

                <!-- The slideshow -->
                <div class="carousel-inner">
                    <div class="carousel-item active ">
                        <div class="d-flex justify-content-center p-4">
                            <div id="JK"></div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="p-4">

                            <div id="JKLahir"></div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="d-flex justify-content-center p-4">
                            <div id="tempatlahir"></div>
                        </div>
                    </div>
                </div>

            </div> --}}
    {{-- <div class="chart-doughnut" style="display: flex;justify-content: center;align-items: center;"> --}}
    {{-- <div class="chart-doughnut d-flex justify-content-center">
                <div id="JK" class=""></div>
            </div>
            <div id="JKLahir"></div>
            <div id="tempatlahir"></div> --}}


    {{-- </div>
    </div> --}}

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Anak</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Ibu</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>BB Lahir</th>
                            <th>PB Lahir</th>
                            <th>Tempat Lahir</th>
                        </tr>
                    </thead>
                    {{-- <tfoot>
                        <tr>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Tempat Lahir</th>
                        </tr>
                    </tfoot> --}}
                    <tbody>
                        @foreach ($Anak as $dataAnak)
                            <tr>
                                <td>{{ $dataAnak->nik }}</td>
                                <td>{{ $dataAnak->ibu->nama }}</td>
                                <td>{{ $dataAnak->nama_bayi }}</td>
                                @if ($dataAnak->jenis_kelamin == 'P')
                                    <td>Perempuan</td>
                                @else
                                    <td>Laki - laki</td>
                                @endif
                                <td>{{ $dataAnak->tanggal_lahir }}</td>
                                <td>{{ $dataAnak->bb_lahir }}</td>
                                <td>{{ $dataAnak->pb_lahir }}</td>
                                <td>{{ $dataAnak->tempat_lahir }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
