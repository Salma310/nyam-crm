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
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <h3 class="card-title mb-0 mr-3"><i class="fas fa-chart-line"></i> Transaksi per Bulan</h3>
                        </div>
                        <div id="filterTransaksiForm" class="form-inline">
                            <label for="start_month" class="mr-2">Start</label>
                            <input type="month" name="start_month" id="start_month" class="form-control mr-2"
                                value="{{ request('start_month', now()->subMonths(11)->format('Y-m')) }}">

                            <label for="end_month" class="mr-2">End</label>
                            <input type="month" name="end_month" id="end_month" class="form-control mr-2"
                                value="{{ request('end_month', now()->format('Y-m')) }}">
                            <button type="button" id="filterTransaksiBtn" class="btn btn-primary"><i
                                    class="fas fa-filter"></i> Filter</button>
                        </div>
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
                                        <button class="btn btn-sm btn-success kirim-wa-btn" data-id="{{ $agen->id }}"
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

        @php
            $tanggal_barang = request()->get(
                'tanggal_barang',
                now()->startOfMonth()->format('Y-m-d') . ' - ' . now()->endOfMonth()->format('Y-m-d'),
            );
            $tanggal_agen = request()->get(
                'tanggal_agen',
                now()->startOfMonth()->format('Y-m-d') . ' - ' . now()->endOfMonth()->format('Y-m-d'),
            );
        @endphp

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0"><i class="fas fa-box"></i> Barang Terlaris</h3>
                        <form id="formFilterTopBarang" class="form-inline">
                            <label for="tanggal_barang" class="mr-2">Tanggal</label>
                            <input type="text" name="tanggal_barang" id="tanggal_barang" class="form-control mr-2"
                                value="{{ $tanggal_barang }}">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        </form>

                    </div>
                    <div class="card-body">
                        <canvas id="topBarangChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0"><i class="fas fa-user"></i> Top Agen</h3>
                        <form id="formFilterTopAgen" class="form-inline">
                            <label for="tanggal_agen" class="mr-2">Tanggal</label>
                            <input type="text" name="tanggal_agen" id="tanggal_agen" class="form-control mr-2"
                                value="{{ $tanggal_agen }}">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <canvas id="topAgenChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

        <script>
            $(function() {
                $('#tanggal').daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    startDate: '{{ request('tanggal') ? explode(' - ', request('tanggal'))[0] : \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}',
                    endDate: '{{ request('tanggal') ? explode(' - ', request('tanggal'))[1] : \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}'
                });
            });
            $(function() {
                let defaultStart =
                    '{{ request('tanggal') ? explode(' - ', request('tanggal'))[0] : \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}';
                let defaultEnd =
                    '{{ request('tanggal') ? explode(' - ', request('tanggal'))[1] : \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}';

                $('#tanggal_barang').daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    startDate: "{{ explode(' - ', $tanggal_barang)[0] }}",
                    endDate: "{{ explode(' - ', $tanggal_barang)[1] }}"
                });

                $('#tanggal_agen').daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    startDate: "{{ explode(' - ', $tanggal_agen)[0] }}",
                    endDate: "{{ explode(' - ', $tanggal_agen)[1] }}"

                });
            });

            $('#formFilterTopBarang').on('submit', function(e) {
                e.preventDefault();
                const tanggal = $('#tanggal_barang').val();

                fetch("{{ route('dashboard.filterTopBarang') }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            tanggal_barang: tanggal
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        renderTopBarangChart(res.labels, res.data);
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal memuat data barang.');
                    });
            });

            $('#formFilterTopAgen').on('submit', function(e) {
                e.preventDefault();
                const tanggal = $('#tanggal_agen').val();

                fetch("{{ route('dashboard.filterTopAgen') }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            tanggal_agen: tanggal
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        renderTopAgenChart(res.labels, res.data, res.jumlah_transaksi);
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal memuat data agen.');
                    });
            });

            let transaksiChartInstance;

            function updateTransaksiChart(labels, data) {
                if (transaksiChartInstance) {
                    transaksiChartInstance.data.labels = labels;
                    transaksiChartInstance.data.datasets[0].data = data;
                    transaksiChartInstance.update();
                }
            }

            document.getElementById('filterTransaksiBtn').addEventListener('click', function() {
                const startMonth = document.getElementById('start_month').value;
                const endMonth = document.getElementById('end_month').value;

                fetch("{{ route('dashboard.filter') }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            start_month: startMonth,
                            end_month: endMonth
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateTransaksiChart(data.labels, data.data);
                    })
                    .catch(error => {
                        console.error("Error filtering data:", error);
                        alert("Gagal memuat data transaksi.");
                    });
            });

            // === Top Barang Chart Re-initialization ===
            let topBarangChart;
            const topBarangCtx = document.getElementById('topBarangChart').getContext('2d');

            function renderTopBarangChart(labels, data) {
                if (topBarangChart) topBarangChart.destroy();
                topBarangChart = new Chart(topBarangCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Terjual',
                            data: data,
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
            }

            // === Top Agen Chart Re-initialization ===
            let topAgenChart;
            const topAgenCtx = document.getElementById('topAgenChart').getContext('2d');

            function renderTopAgenChart(labels, data, jumlahTransaksi) {
                if (topAgenChart) topAgenChart.destroy();
                topAgenChart = new Chart(topAgenCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Nilai Transaksi (Rp)',
                            data: data,
                            backgroundColor: '#28a745'
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.x;
                                        const index = context.dataIndex;
                                        const jumlah = jumlahTransaksi[index] ?? 0;
                                        return [
                                            'Total Transaksi: Rp ' + value.toLocaleString('id-ID'),
                                            'Jumlah Transaksi: ' + jumlah + 'x'
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Simpan instansi Chart ke variabel global agar bisa diupdate
            transaksiChartInstance = new Chart(document.getElementById('transaksiChart').getContext('2d'), {
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
            document.addEventListener('DOMContentLoaded', function() {
                renderTopBarangChart(@json($topBarang->pluck('nama_barang')), @json($topBarang->pluck('total_terjual')));
                renderTopAgenChart(@json($topAgen->pluck('nama')), @json($topAgen->pluck('total_transaksi')),
                    @json($topAgen->pluck('jumlah_transaksi')));
                // Kirim WhatsApp Button
                document.querySelectorAll('.kirim-wa-btn').forEach(button => {
                    button.addEventListener('click', function() {
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
                                            Swal.fire('Gagal!', data.error ||
                                                'Terjadi kesalahan.', 'error');
                                        }
                                    })
                                    .catch(() => {
                                        Swal.fire('Gagal!', 'Gagal mengirim permintaan.',
                                            'error');
                                    });
                            }
                        });
                    });
                });

            });
        </script>
    @endpush
@endsection
