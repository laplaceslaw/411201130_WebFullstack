<?php
use Carbon\Carbon;
?>

<!DOCTYPE html>
<html lang="en">

@include('partials.head')
<link rel="shortcut icon" type="image/png" href="https://kucinkputih.com/.UNDIRA/cat--v3.png"/>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">
        @include('layouts.sidebar')

        <div class="content-wrapper">

            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>{{ $sub }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">Home</a></li>
                                <li class="breadcrumb-item active">{{ $sub }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">

                <!--  Donut chart Lokasi dan Barang -->
                <div class="row justify-content-center">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-gray"><h5><b>Chart Lokasi</b></h5></div>
                            <div class="card-body">
                                <div id="donut-chart" style="height: 250px;"></div>
                                <a href="javascript:;"
                                    onclick="alert('Ini adalah lokasi yang memiliki jumlah barang dikirim lebih dari 100 (>100) dalam 1 bulan')"
                                    class="btn btn-primary w-100 mt-3">Keterangan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-gray"><h5><b>Chart Barang</b></h5></div>
                            <div class="card-body">
                                <div id="donut-chart2" style="height: 250px;"></div>
                                <a href="javascript:;"
                                    onclick="alert('Ini adalah barang yang memiliki harga barang lebih dari 1.000 (>1.000) dalam 1 tahun')"
                                    class="btn btn-primary w-100 mt-3">Keterangan</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  Dashboard Total -->
                <div class="row justify-content-left mb-3">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-gray">
                            <div class="card-body">
                                <p><b>Total Keseluruhan Pengiriman</b><br><i>Dalam 3 Bulan</i></p>
                                @php
                                    $threeMonthsAgo = Carbon::now()->subMonths(3);
                                    
                                    $totalShipments = DB::table('pengiriman')
                                        ->where('tanggal', '>=', $threeMonthsAgo)
                                        ->count();
                                @endphp
                                <h3 class="fs-3">{{ $totalShipments }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-gray">
                            <div class="card-body">
                                <p><b>Lokasi Terbanyak Sebagai Tujuan Pengiriman</b><br><i>Dalam 1 Bulan</i></p>
                                @php
                                    $oneMonthAgo = Carbon::now()->subMonths(1);
                                    
                                    $mostFrequentDestination = DB::table('pengiriman')
                                        ->join('lokasi', 'pengiriman.lokasi_id', '=', 'lokasi.id')
                                        ->select('lokasi.nama_lokasi', DB::raw('COUNT(*) as total'))
                                        ->where('pengiriman.tanggal', '>=', $oneMonthAgo)
                                        ->groupBy('lokasi.nama_lokasi')
                                        ->orderByDesc('total')
                                        ->first();
                                    
                                    $namaLokasi = $mostFrequentDestination ? $mostFrequentDestination->nama_lokasi : null;
                                    
                                @endphp
                                <h3 class="fs-3">{{ $namaLokasi }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-gray">
                            <div class="card-body">
                                <p><b>Jumlah Barang Terbanyak Sebagai Pengiriman</b><br><i>Dalam 1 Tahun</i></p>
                                @php
                                    $oneYearAgo = Carbon::now()->subYear(1);
                                    
                                    $highestQuantity = DB::table('pengiriman')
                                        ->select('barang_id', DB::raw('SUM(jumlah_barang) as total_quantity'))
                                        ->where('tanggal', '>=', $oneYearAgo)
                                        ->groupBy('barang_id')
                                        ->orderByDesc('total_quantity')
                                        ->first();
                                    
                                    $productName = null;
                                    if ($highestQuantity) {
                                        $productName = DB::table('barang')
                                            ->where('id', $highestQuantity->barang_id)
                                            ->value('nama_barang');
                                    }
                                @endphp

                                @php
                                    $totalQuantity = $highestQuantity ? $highestQuantity->total_quantity : null;
                                    $totalQuantity = htmlspecialchars($totalQuantity);
                                @endphp
                                <h3 class="fs-3">{{ $productName }} ({{ $totalQuantity }})</h3>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>UAS Web Fullstack.</b> 411201130 | Indra Surya Hanjaya
            </div>
            <strong>Copyright &copy; 2023 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights
            reserved. <b>Version</b> 3.2.0
        </footer>
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>
    @include('partials.script')

    <!-- Donut Chart -->
    <script>
        function updateChart() {
            $.ajax({
                url: '/donut_chart/v1',
                method: 'GET',
                success: function(data) {
                    var total = data.reduce((sum, item) => sum + item.total, 0);

                    var labels = data.map(location => {
                        var percentage = (location.total / total) * 100;
                        return `${location.nama_lokasi} - ${location.total} (${percentage.toFixed(2)}%)`;
                    });
                    var series = data.map(location => location.total);

                    var chartData = {
                        labels: labels,
                        series: series
                    };

                    var chartOptions = {
                        donut: true,
                        donutWidth: 60,
                        donutSolid: true,
                        startAngle: 270,
                        showLabel: true,
                    };

                    new Chartist.Pie('#donut-chart', chartData, chartOptions);
                }
            });
        }

        updateChart();
        setInterval(updateChart, 5000);
    </script>
    
    <!-- Donut Chart v2 -->
    <script>
        function updateChart2() {
            $.ajax({
                url: '/donut_chart/v2',
                method: 'GET',
                success: function(data) {
                    var total = data.reduce((sum, item) => sum + item.total, 0);

                    var labels = data.map(item => {
                        var percentage = (item.total / total) * 100;
                        return `${item.nama_barang} - ${item.total} (${percentage.toFixed(2)}%)`;
                    });
                    var series = data.map(item => item.total);

                    var chartData = {
                        labels: labels,
                        series: series
                    };

                    var chartOptions = {
                        donut: true,
                        donutWidth: '100%',
                        donutSolid: true,
                        startAngle: 0,
                        showLabel: true,
                    };

                    new Chartist.Pie('#donut-chart2', chartData, chartOptions);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        updateChart2();
        setInterval(updateChart2, 5000);
    </script>

</body>

</html>