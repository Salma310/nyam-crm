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
                                    {{-- <li class="list-group-item"> --}}
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                        <strong>{{ $agen->nama }}</strong><br>
                                        <small>Terakhir transaksi:
                                            {{ $agen->terakhir_transaksi ? \Carbon\Carbon::parse($agen->terakhir_transaksi)->format('d M Y') : 'Belum pernah' }}</small>
                                        </div>
                                        <button class="btn btn-sm btn-success kirim-wa-btn"
                                            data-id="{{ $agen->id }}"
                                            data-nama="{{ $agen->nama }}">
                                            <i class="fab fa-whatsapp"></i>
                                        </button>
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

                // new Chart(document.getElementById('topAgenChart').getContext('2d'), {
                const ctx = document.getElementById('topAgenChart').getContext('2d');
                const agenIds = @json($topAgen->pluck('id'));
                const agenLabels = @json($topAgen->pluck('nama'));
                const agenData = @json($topAgen->pluck('total_transaksi'));
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($topAgen->pluck('nama')),
                        // labels:agenIds,
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
                        onClick: function (event, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const agenId = agenIds[index];
                                window.location.href = `/agen/${agenId}/show`;
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Kirim WhatsApp Button
                document.querySelectorAll('.kirim-wa-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const agenId = this.dataset.id;
                        console.log("Agen ID:", agenId); 
                        const agenNama = this.dataset.nama;

                        Swal.fire({
                            title: 'Kirim WhatsApp?',
                            text: `Kirim reminder ke agen ${agenNama}?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#6c5ce7',
                            cancelButtonColor: '#636e72',
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/agen/${agenId}/send-reminder`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.message) {
                                        Swal.fire('Berhasil!', data.message, 'success');
                                    } else {
                                        Swal.fire('Gagal!', data.error || 'Terjadi kesalahan.', 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Gagal!', 'Gagal mengirim permintaan.', 'error');
                                });
                            }
                        });
                    });
                });

            });
        </script>
    @endpush
@endsection
