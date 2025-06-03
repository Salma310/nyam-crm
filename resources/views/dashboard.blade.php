@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <section class="col-lg-7 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-box"></i> Barang Terlaris</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="topBarangChart" height="200"></canvas>
                    </div>
                </div>
            </section>
            <section class="col-lg-5 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> Agen Teraktif</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="topAgenChart" height="200"></canvas>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Transaksi per Hari</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="transaksiChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <section class="col-lg-5 connectedSortable">
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
                                        <small>
                                            Terakhir transaksi:
                                            {{ $agen->terakhir_transaksi ? \Carbon\Carbon::parse($agen->terakhir_transaksi)->format('d M Y') : 'Belum pernah' }}
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>
        </div>
        {{-- <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Transaksi per Bulan</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="transaksiChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <section class="col-lg-5 connectedSortable">
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
                                        <small>
                                            Terakhir transaksi:
                                            {{ $agen->terakhir_transaksi ? \Carbon\Carbon::parse($agen->terakhir_transaksi)->format('d M Y') : 'Belum pernah' }}
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>
        </div> --}}
    </div>
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Inisialisasi chart dimulai');

                // === Transaksi per Bulan ===
                const transaksiCanvas = document.getElementById('transaksiChart');
                if (transaksiCanvas) {
                    const ctx = transaksiCanvas.getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($labels),
                            datasets: [{
                                label: 'Jumlah Transaksi',
                                data: @json($data),
                                fill: true,
                                backgroundColor: 'rgba(60,141,188,0.2)',
                                borderColor: 'rgba(60,141,188,1)',
                                tension: 0.4,
                                pointBackgroundColor: 'rgba(60,141,188,1)',
                                pointBorderColor: '#fff',
                                pointHoverRadius: 6,
                                pointRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    },
                                    title: {
                                        display: true,
                                        text: 'Jumlah Transaksi'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Bulan'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: '#333',
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                axis: 'x',
                                intersect: false
                            }
                        }
                    });
                } else {
                    console.error('Elemen canvas dengan id "transaksiChart" tidak ditemukan!');
                }

                // === Barang Terlaris ===
                const barangLabels = @json($topBarang->pluck('nama_barang'));
                const barangData = @json($topBarang->pluck('total_terjual'));

                const topBarangCanvas = document.getElementById('topBarangChart');
                if (topBarangCanvas) {
                    new Chart(topBarangCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: barangLabels,
                            datasets: [{
                                label: 'Jumlah Terjual',
                                data: barangData,
                                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545',
                                    '#17a2b8'
                                ],
                                borderColor: '#ddd',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }

                // === Agen Teraktif ===
                const agenLabels = @json($topAgen->pluck('nama'));
                const agenData = @json($topAgen->pluck('total_transaksi'));

                const topAgenCanvas = document.getElementById('topAgenChart');
                if (topAgenCanvas) {
                    new Chart(topAgenCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: agenLabels,
                            datasets: [{
                                label: 'Jumlah Transaksi',
                                data: agenData,
                                backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#007bff',
                                    '#dc3545'
                                ],
                                borderColor: '#ddd',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
