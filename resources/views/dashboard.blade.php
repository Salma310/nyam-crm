@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- KPI Cards -->
            <div class="col-md-3">
                <div class="small-box bg-gradient-primary">
                    <div class="inner">
                        <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-gradient-success">
                    <div class="inner">
                        <h3>{{ $totalProductsSold }}</h3>
                        <p>Products Sold</p>
                    </div>
                    <div class="icon"><i class="fas fa-box"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-gradient-info">
                    <div class="inner">
                        <h3>{{ $totalProducts }}</h3>
                        <p>Total Products</p>
                    </div>
                    <div class="icon"><i class="fas fa-cubes"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h3>{{ $totalAgents }}</h3>
                        <p>Total Agen</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-gradient-secondary" id="wablas-status-box">
                    <div class="inner">
                        <h3 id="wablas-status">...</h3>
                        <p>Status WhatsApp</p>
                    </div>
                    <div class="icon"><i class="fab fa-whatsapp"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Transaksi per Bulan</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="transaksiChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bell"></i> Reminder: Agen Tidak Aktif</h3>
                    </div>
                    <div class="card-body" style="max-height: 250px; overflow-y:auto;">
                        @if ($inactiveAgents->isEmpty())
                            <p class="text-dark">Semua agen aktif dalam 30 hari terakhir.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach ($inactiveAgents as $agen)
                                    <li class="list-group-item">
                                        <strong>{{ $agen->nama }}</strong><br>
                                        <small>Terakhir transaksi:
                                            {{ $agen->terakhir_transaksi ? \Carbon\Carbon::parse($agen->terakhir_transaksi)->format('d M Y') : 'Belum pernah' }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-box"></i> Barang Terlaris</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="topBarangChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> Agen Teraktif</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="topAgenChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const transaksiChart = new Chart(document.getElementById('transaksiChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: @json($data),
                            fill: true,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            tension: 0.4,
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            legend: {
                                display: true
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });

                new Chart(document.getElementById('topBarangChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($topBarang->pluck('nama_barang')),
                        datasets: [{
                            label: 'Jumlah Terjual',
                            data: @json($topBarang->pluck('total_terjual')),
                            backgroundColor: '#007bff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                new Chart(document.getElementById('topAgenChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($topAgen->pluck('nama')),
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: @json($topAgen->pluck('total_transaksi')),
                            backgroundColor: '#28a745'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

            function updateWablasStatus() {
                fetch("{{ url('/wablas-status') }}")
                    .then(response => response.json())
                    .then(data => {
                        const statusEl = document.getElementById("wablas-status");
                        const boxEl = document.getElementById("wablas-status-box");

                        statusEl.innerText = data.status;

                        // Ubah warna box tergantung status
                        boxEl.classList.remove("bg-gradient-success", "bg-gradient-danger", "bg-gradient-warning",
                            "bg-gradient-secondary");

                        switch (data.status) {
                            case "CONNECTED":
                                boxEl.classList.add("bg-gradient-success");
                                break;
                            case "DISCONNECTED":
                                boxEl.classList.add("bg-gradient-danger");
                                break;
                            case "EXPIRED":
                                boxEl.classList.add("bg-gradient-warning");
                                break;
                            default:
                                boxEl.classList.add("bg-gradient-secondary");
                        }
                    })
                    .catch(error => {
                        console.error("Gagal mengambil status Wablas:", error);
                        document.getElementById("wablas-status").innerText = "ERROR";
                        document.getElementById("wablas-status-box").classList.add("bg-gradient-danger");
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateWablasStatus();
                setInterval(updateWablasStatus, 10000); // update setiap 10 detik
            });
        </script>
    @endpush
@endsection
