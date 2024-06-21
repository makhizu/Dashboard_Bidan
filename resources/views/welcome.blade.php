@extends('layouts.main')

@section('js')
    <script>
        // data general

        var year = @json($year);
        // console.log(year);
        var category = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // fungsi chart line utama
        function lineChart(data) {
            var chart = data.map(($item) => ({
                name: $item.month,
                y: $item.count,
                drilldown: $item.month,
            }));

            return chart;
        }
    </script>
    {{-- JUMLAH KUNJUNGAN LAYANAN PER BULAN --}}
    <script>
        var KB = @json($kbData);
        const KBdata = KB.map(item => item.count);
        // console.log(KBdata);

        var imunisasi = @json($imunisasiData);
        const imunisasidata = imunisasi.map(item => item.count);

        var kehamilan = @json($kehamilanData);
        const kehamilandata = kehamilan.map(item => item.count);

        var persalinan = @json($persalinanData);
        const persalinandata = persalinan.map(item => item.count);

        var rerata = @json($rerata);
        const reratadata = rerata.map(item => item.rata);

        // console.log(reratadata);


        Highcharts.chart('batang', {
            title: {
                text: 'Kunjungan Pasien per Layanan',
                align: 'left'
            },
            subtitle: {
                text: 'Tahun ' + year,
                align: 'left'
            },
            xAxis: {
                // categories: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                //     'Oktober', 'November', 'Desember'
                // ]
                categories: category
                // bulan per tahun
            },
            yAxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                }
            },
            tooltip: {
                valueSuffix: ' kunjungan'
            },
            plotOptions: {
                series: {
                    borderRadius: '25%'
                }
            },
            series: [{
                    type: 'column',
                    name: 'KB',
                    data: KBdata
                }, {
                    type: 'column',
                    name: 'Imunisasi',
                    data: imunisasidata
                }, {
                    type: 'column',
                    name: 'Kehamilan',
                    data: kehamilandata
                }, {
                    type: 'column',
                    name: 'Persalinan',
                    data: persalinandata
                }, {
                    type: 'line',
                    step: 'center',
                    name: 'Rata - rata',
                    data: reratadata,
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                },
                // {
                //     type: 'pie',
                //     name: 'Total',
                //     data: [{
                //         name: '2020',
                //         y: 619,
                //         color: Highcharts.getOptions().colors[0], // 2020 color
                //         dataLabels: {
                //             enabled: true,
                //             distance: -50,
                //             format: '{point.total} M',
                //             style: {
                //                 fontSize: '15px'
                //             }
                //         }
                //     }, {
                //         name: '2021',
                //         y: 586,
                //         color: Highcharts.getOptions().colors[1] // 2021 color
                //     }, {
                //         name: '2022',
                //         y: 647,
                //         color: Highcharts.getOptions().colors[2] // 2022 color
                //     }],
                //     center: [75, 65],
                //     size: 100,
                //     innerSize: '70%',
                //     showInLegend: false,
                //     dataLabels: {
                //         enabled: false
                //     }
                // }
            ]
        });
    </script>

    {{-- GRAFIK PERBANDINGAN JUMLAH PENGUNJUNG PER LAYANAN --}}
    <script>
        // masukkan data ke variable
        var mainKunjungan = @json($countLayanan);

        // console.log(mainKunjungan);

        // menyesuaikan data dengan format grafik
        const rmainKunjungan = mainKunjungan.map(($item) => ({
            name: $item.name,
            y: $item.count,
        }));

        // console.log(rmainKunjungan);

        // Create the chart
        Highcharts.chart('perbandingan', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Kunjungan Per Layanan',
                align: 'left'
            },
            subtitle: {
                text: 'Tahun ' + year,
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
                        enabled: false,
                        format: '{point.name}: {point.y} Pasien'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Kunjungan ({point.percentage:.1f}%)<br/>'
            },

            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                innerSize: '50%',
                data: rmainKunjungan
            }],
        });
    </script>

    {{-- KB --}}
    <script>
        var KB = @json($kbData);
        let dataKB = lineChart(@json($kbData));

        // console.log(dataKB);

        var drillKB = @json($KBdrill);
        // console.log(drillKB);
        var drilldownData = drillKB.map(function(item) {
            return {
                type: 'column',
                name: item.month,
                id: item.month,
                data: item.data.map(function(akseptor) {
                    return [akseptor.akseptor, akseptor.count];
                })
            };
        });
        // console.log(drilldownData);


        // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
        Highcharts.chart('KB', {
            // chart: {
            //     type: 'line'
            // },
            title: {
                text: 'Jumlah Kunjungan Pelayanan KB'
            },
            subtitle: {
                text: 'Tahun ' + year,
                align: 'center'
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                },
                column: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Kunjungan<br/>'
            },
            series: [{
                name: 'KB',
                type: 'line',
                data: dataKB
            }],
            drilldown: {
                series: drilldownData,
                tooltip: {
                    pointFormat: '<b>{point.name}</b>: {point.y} kg'
                },
            }
        });

        // {{-- GRAFIK PERBANDINGAN JUMLAH akseptor --}}    
        // masukkan data ke variable
        var mainKB = @json($chartKB);

        // console.log(mainKB);

        // menyesuaikan data dengan format grafik
        const rmainKB = mainKB.map(($item) => ({
            name: $item.label,
            y: $item.count,
        }));

        // console.log(rmainKB);

        // Create the chart
        Highcharts.chart('chartKB', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Jumlah Penerima Akseptor',
                align: 'left'
            },
            subtitle: {
                text: 'Tahun ' + year,
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
                        enabled: false,
                        format: '{point.name}: {point.y} Pasien'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Ibu ({point.percentage:.1f}%)<br/>'
            },

            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                innerSize: '50%',
                data: rmainKB
            }],
        });
    </script>

    {{-- KEHAMILAN --}}
    <script>
        // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
        Highcharts.chart('kehamilan', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Jumlah Kunjungan Pelayanan Kehamilan'
            },
            subtitle: {
                text: 'Tahun ' + year,
                align: 'center'
            },
            xAxis: {
                categories: category
            },
            yAxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                },
                column: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            series: [{
                name: 'Kehamilan',
                data: kehamilandata
            }]
        });

        // scatter LILA
        var scatterData = @json($scatterLila);
        // console.log(scatterData);

        Highcharts.chart('scatterLila', {
            chart: {
                type: 'scatter',
                zoomType: 'xy'
            },
            title: {
                text: 'Sebaran BB dan LILA Ibu Hamil'

            },
            subtitle: {
                text: 'Tahun ' + year,
                align: 'center'
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
        var Imunisasi = @json($imunisasiData);
        let dataImunisasi = lineChart(@json($imunisasiData));

        console.log(dataImunisasi);

        var drillImunisasi = @json($Imunisasidrill);
        // console.log(drillImunisasi);
        var drilldownData = drillImunisasi.map(function(item) {
            return {
                type: 'column',
                name: item.month,
                id: item.month,
                data: item.data.map(function(imunisasi) {
                    return [imunisasi.label, imunisasi.count];
                })
            };
        });

        // console.log(drilldownData);
        Highcharts.chart('imunisasi', {
            // chart: {
            //     type: 'line'
            // },
            title: {
                text: 'Jumlah Kunjungan Pelayanan Imunisasi Anak'
            },
            subtitle: {
                text: 'Tahun ' + year,
                align: 'center'
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                },
                column: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Kunjungan<br/>'
            },
            series: [{
                name: 'Imunisasi',
                type: 'line',
                data: dataImunisasi
            }],
            drilldown: {
                series: drilldownData,
                tooltip: {
                    pointFormat: '<b>{point.name}</b>: {point.y} kg'
                },
            }
        });
        // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature


        // {{-- GRAFIK PERBANDINGAN JUMLAH VAKSIN     --}}
        // masukkan data ke variable
        var mainImunisasi = @json($chartImunisasi);

        // console.log(mainImunisasi);

        // menyesuaikan data dengan format grafik
        const rmainImunisasi = mainImunisasi.map(($item) => ({
            name: $item.label,
            y: $item.count,
        }));

        // console.log(rmainImunisasi);

        // Create the chart
        Highcharts.chart('chartImunisasi', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Jumlah Penerima Vaksin',
                align: 'left'
            },
            subtitle: {
                text: 'Tahun ' + year,
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
                        enabled: false,
                        format: '{point.name}: {point.y} Pasien'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Anak ({point.percentage:.1f}%)<br/>'
            },

            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                innerSize: '50%',
                data: rmainImunisasi
            }],
        });
    </script>

    {{-- persalinan --}}
    <script>
        var Persalinan = @json($persalinanData);
        let dataPersalinan = lineChart(@json($persalinanData));

        // console.log(dataPersalinan);

        var drillPersalinan = @json($Persalinandrill);
        // console.log(drillPersalinan);
        var drilldownData = drillPersalinan.map(function(item) {
            return {
                type: 'column',
                name: item.month,
                id: item.month,
                data: item.data.map(function(persalinan) {
                    return [persalinan.persalinan, persalinan.count];
                })
            };
        });

        // console.log(drilldownData);
        Highcharts.chart('persalinan', {
            // chart: {
            //     type: 'line'
            // },
            title: {
                text: 'Jumlah Kunjungan Pelayanan Persalinan'
            },
            subtitle: {
                text: 'Tahun ' + year,
                align: 'center'
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                },
                column: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Kunjungan<br/>'
            },
            series: [{
                name: 'Persalinan',
                type: 'line',
                data: dataPersalinan
            }],
            drilldown: {
                tooltip: {
                    pointFormat: '<b>{point.name}</b>: {point.y}'
                },
                series: drilldownData,
            },
        });
        // {{-- GRAFIK PERBANDINGAN JUMLAH persalinan --}}    
        // masukkan data ke variable
        var mainPersalinan = @json($mainPersalinan);

        // console.log(mainPersalinan);

        // menyesuaikan data dengan format grafik
        const rmainPersalinan = mainPersalinan.map(($item) => ({
            name: $item.label,
            y: $item.count,
        }));

        // console.log(rmainPersalinan);

        // Create the chart
        Highcharts.chart('chartPersalinan', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Jumlah Persalinan Normal dan Rujuk',
                align: 'left'
            },
            subtitle: {
                text: 'Tahun ' + year,
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
                        enabled: false,
                        format: '{point.name}: {point.y} Pasien'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Ibu ({point.percentage:.1f}%)<br/>'
            },

            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                innerSize: '50%',
                data: rmainPersalinan
            }],
        });
    </script>
    <script>
        $(function() {
            // Create the chart
            Highcharts.chart('tes', {
                title: {
                    text: 'Browser market shares. January, 2015 to May, 2015'
                },
                subtitle: {
                    text: 'Click the columns to view versions. Source: <a href="http://netmarketshare.com">netmarketshare.com</a>.'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Total percent market share'
                    }

                },
                legend: {
                    enabled: false
                },
                // plotOptions: {
                //     series: {
                //         borderWidth: 0,
                //         dataLabels: {
                //             enabled: true,
                //             format: '{point.y:.1f}%'
                //         }
                //     }
                // },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f}%'
                        },
                        enableMouseTracking: true
                    },
                    column: { // Added plotOptions for column chart
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        },
                        enableMouseTracking: true
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                },

                series: [{
                    name: 'Brands',
                    type: 'line',
                    colorByPoint: true,
                    data: [{
                        name: 'Microsoft Internet Explorer',
                        y: 56.33,
                        drilldown: 'Microsoft Internet Explorer'
                    }, {
                        name: 'Chrome',
                        y: 24.03,
                        drilldown: 'Chrome'
                    }, {
                        name: 'Firefox',
                        y: 10.38,
                        drilldown: 'Firefox'
                    }, {
                        name: 'Safari',
                        y: 4.77,
                        drilldown: 'Safari'
                    }, {
                        name: 'Opera',
                        y: 0.91,
                        drilldown: 'Opera'
                    }, {
                        name: 'Proprietary or Undetectable',
                        y: 0.2,
                        drilldown: null
                    }]
                }],
                drilldown: {
                    tooltip: {
                        pointFormat: '<b>{point.name}</b>: {point.y}'
                    },
                    series: [{
                        type: 'column',
                        name: 'Microsoft Internet Explorer',
                        id: 'Microsoft Internet Explorer',
                        data: [
                            [
                                'v11.0',
                                24.13
                            ],
                            [
                                'v8.0',
                                17.2
                            ],
                            [
                                'v9.0',
                                8.11
                            ],
                            [
                                'v10.0',
                                5.33
                            ],
                            [
                                'v6.0',
                                1.06
                            ],
                            [
                                'v7.0',
                                0.5
                            ]
                        ]
                    }, {
                        type: 'column',
                        name: 'Chrome',
                        id: 'Chrome',
                        data: [
                            [
                                'v40.0',
                                5
                            ],
                            [
                                'v41.0',
                                4.32
                            ],
                            [
                                'v42.0',
                                3.68
                            ],
                            [
                                'v39.0',
                                2.96
                            ],
                            [
                                'v36.0',
                                2.53
                            ],
                            [
                                'v43.0',
                                1.45
                            ],
                            [
                                'v31.0',
                                1.24
                            ],
                            [
                                'v35.0',
                                0.85
                            ],
                            [
                                'v38.0',
                                0.6
                            ],
                            [
                                'v32.0',
                                0.55
                            ],
                            [
                                'v37.0',
                                0.38
                            ],
                            [
                                'v33.0',
                                0.19
                            ],
                            [
                                'v34.0',
                                0.14
                            ],
                            [
                                'v30.0',
                                0.14
                            ]
                        ]
                    }, {
                        type: 'column',
                        name: 'Firefox',
                        id: 'Firefox',
                        data: [
                            [
                                'v35',
                                2.76
                            ],
                            [
                                'v36',
                                2.32
                            ],
                            [
                                'v37',
                                2.31
                            ],
                            [
                                'v34',
                                1.27
                            ],
                            [
                                'v38',
                                1.02
                            ],
                            [
                                'v31',
                                0.33
                            ],
                            [
                                'v33',
                                0.22
                            ],
                            [
                                'v32',
                                0.15
                            ]
                        ]
                    }, {
                        type: 'column',
                        name: 'Safari',
                        id: 'Safari',
                        data: [
                            [
                                'v8.0',
                                2.56
                            ],
                            [
                                'v7.1',
                                0.77
                            ],
                            [
                                'v5.1',
                                0.42
                            ],
                            [
                                'v5.0',
                                0.3
                            ],
                            [
                                'v6.1',
                                0.29
                            ],
                            [
                                'v7.0',
                                0.26
                            ],
                            [
                                'v6.2',
                                0.17
                            ]
                        ]
                    }, {
                        type: 'column',
                        name: 'Opera',
                        id: 'Opera',
                        data: [
                            [
                                'v12.x',
                                0.34
                            ],
                            [
                                'v28',
                                0.24
                            ],
                            [
                                'v27',
                                0.17
                            ],
                            [
                                'v29',
                                0.16
                            ]
                        ]
                    }],
                    // Default tooltip options for all drilldown series

                }
            });
        });
    </script>

    <script type="text/javascript">
        Highcharts.chart('container', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Browser market shares. January, 2022',
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
                    dataLabels: [{
                        enabled: true,
                        distance: 15,
                        format: '{point.name}'
                    }, {
                        enabled: true,
                        distance: '-30%',
                        filter: {
                            property: 'percentage',
                            operator: '>',
                            value: 5
                        },
                        format: '{point.y:.1f}%',
                        style: {
                            fontSize: '0.9em',
                            textOutline: 'none'
                        }
                    }]
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
@endsection

@section('content')
    <!-- Page Heading -->
    <div class="row">

        <div class="col d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Tahun {{ $year }}</h1>
        </div>
        <div class="col d-sm-flex align-items-center justify-content-end mb-4">
            <div class="row pr-4">
                <form action="{{ route('home.index') }}" method="GET">
                    @csrf
                    @method('POST')
                    <div class="input-group">
                        <select class="custom-select col-7" id="inputGroupSelect04" name="tahun"
                            aria-label="Example select with button addon">
                            @foreach ($yearList as $list)
                                <option {{ $list == $year ? 'selected' : '' }} value="{{ $list }}">
                                    {{ $list }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-append col-5 p-0">
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kunjungan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah }} Pasien</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Rerata Kunjungan Per Bulan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rerataMonth }} Pasien</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Imunisasi Terbanyak
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    @if ($mostVacin)
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mostVacin['label'] }}</div>
                                    @else
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crutch fa-2x text-gray-300"></i>
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
                            @if ($mostKB == null)
                                <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                            @else
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mostKB['label'] }}</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-prescription-bottle-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- DIAGRAM UTAMA ------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-9 mb-4">
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

        <div class="col-lg-3 mb-4">
            <div class="card shadow">
                {{-- <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Diagram
                        </h6>
                    </div> --}}
                <div class="card-body">
                    <div class="center">
                        <div id="perbandingan" class="perbandingan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END DIAGRAM UTAMA ------------------------------------------------ --}}

    {{-- -------------------------------------------- DIAGRAM KB ------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-9 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('kb.index') }}"
                            style="text-decoration: none">KB</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="KB"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('kb.index') }}"
                            style="text-decoration: none">KB</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="chartKB" class="chartKB"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END DIAGRAM KB ------------------------------------------------ --}}

    {{-- -------------------------------------------- DIAGRAM KEHAMILAN ------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('kehamilan.index') }}"
                            style="text-decoration: none">Kehamilan</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="kehamilan"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('kehamilan.index') }}"
                            style="text-decoration: none">Kehamilan</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="scatterLila" class="scatterLila"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END DIAGRAM KEHAMILAN ------------------------------------------------ --}}

    {{-- -------------------------------------------- DIAGRAM IMUNISASI ------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('imunisasi.index') }}"
                            style="text-decoration: none">Imunisasi</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="imunisasi"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('imunisasi.index') }}"
                            style="text-decoration: none"> Imunisasi</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="chartImunisasi" class="chartImunisasi"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END DIAGRAM IMUNISASI ------------------------------------------------ --}}

    {{-- -------------------------------------------- DIAGRAM PERSALINAN ------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('persalinan.index') }}"
                            style="text-decoration: none">Persalinan</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="persalinan"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><a href="{{ route('persalinan.index') }}"
                            style="text-decoration: none">Persalinan</a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="center">
                        <div id="chartPersalinan" class="chartPersalinan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END DIAGRAM PERSALINAN ------------------------------------------------ --}}

    {{-- -------------------------------------------- DIAGRAM test ------------------------------------------------ --}}
    <div class="row">
        <div class="col-lg-9 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="center">
                        <figure class="highcharts-figure">
                            <div id="container"></div>
                            <p class="highcharts-description">
                                Pie chart where the individual slices can be clicked to expose more
                                detailed data.
                            </p>
                        </figure>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="center">
                        <div id="charttest" class="charttest"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------- END DIAGRAM test ------------------------------------------------ --}}
@endsection
