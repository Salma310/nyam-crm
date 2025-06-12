@extends('layouts.app')

@section('content')
    <html>

    <head>
        <title>
            Transaksi
        </title>
        <style>
            body {
                background-color: #f4f7f6;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }

            .content {
                padding: 15px;
            }

            .content .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                background-color: white;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                flex-wrap: wrap;
                /* Agar elemen turun jika ruang tidak cukup */
            }

            .group-btn {
                display: flex;
                align-items: center;
                gap: 30px;
                /* Jarak antar tombol */
            }

            .content .header:hover {
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }

            .content .header .search-box {
                margin-left: auto;
                /* Memastikan search box berada di kanan */
                display: flex;
                align-items: center;
                position: relative;
            }

            .content .header .search-box input {
                border-radius: 25px;
                border: 2px solid #e0e0e0;
                padding: 12px 20px 12px 40px;
                width: 300px;
                font-size: 16px;
                background-color: #f9f9f9;
                transition: all 0.4s ease;
            }

            .content .header .search-box input:focus {
                border-color: #4a90e2;
                background-color: white;
                box-shadow: 0 0 15px rgba(74, 144, 226, 0.2);
                outline: none;
            }

            .content .header .search-box i {
                position: absolute;
                left: 15px;
                color: #a0a0a0;
                transition: color 0.3s ease;
            }

            .content .header .search-box input:focus+i {
                color: #4a90e2;
            }

            .content .header .btn-primary {
                background: linear-gradient(to right, #4a90e2, #2c3e50);
                border: none;
                border-radius: 25px;
                padding: 12px 25px;
                font-size: 16px;
                font-weight: 600;
                display: flex;
                align-items: center;
                transition: all 0.4s ease;
                box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
            }

            .content .header .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
            }

            .content .header .btn-primary i {
                margin-right: 10px;
            }

            .content .table-container {
                background-color: white;
                border-radius: 12px;
                padding: 25px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                overflow-x: auto;
            }

            .content .table-container table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 10px;
            }

             .content .table-container table thead tr th {
                background-color: #FFC36B;
                font-weight: 600;
            }

            .content .table-container table thead tr th:first-child {
                border-top-left-radius: 12px;
            }

            .content .table-container table thead tr th:last-child {
                border-top-right-radius: 12px;
            }

            .content .table-container table th {
                padding: 25px;
                text-align: left;
                font-weight: 600;
                color: #2c3e50;
                border-bottom: 2px solid #e0e0e0;
            }

            .content .table-container table td {
                padding: 15px;
                color: #34495e;
                background-color: #f9f9f9;
                transition: all 0.3s ease;
            }

            .content .table-container table tr {
                margin-bottom: 10px;
            }

            .content .table-container table tr:hover td {
                background-color: #f1f3f4;
                transform: scale(1.01);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .content .table-container table td .btn {
                /* margin: 0 5px; */
                border-radius: 20px;
                padding: 8px 15px;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .content .table-container table td .btn-light {
                background-color: #f1f3f4;
                color: #34495e;
                border: none;
            }

            .content .table-container table td .btn-light:hover {
                background-color: #e0e2e4;
                transform: translateY(-2px);
            }

            .content .table-container table td .btn-light.text-danger:hover {
                background-color: #ffebee;
                color: #d32f2f;
            }

            .filter_jenis_event {
                width: 250px;
                font-size: 14px;
                border-radius: 8px;
            }
        </style>
    </head>

    <body>
        <div class="content flex-grow-1">
            <div class="header">
                <div class="group-btn">
                    <button class="btn btn-primary" onclick="modalAction('{{ url('transaksi/create') }}')">
                        <i class="fas fa-plus"></i>Add Penjualan</button>
                </div>
                <div class="search-box">
                    <input id="searchInput" onkeyup="searchTable()" placeholder="Search" type="text" />
                    <i class="fas fa-search">
                    </i>
                </div>
            </div>
            <div class="table-container table-responsive mt-4">
                <div class="filter-container mb-3 d-flex justify-content-start align-items-center">
                    <div class="position-relative" style="max-width: 260px; width: 100%;">
                        <input
                            type="text"
                            id="dateRange"
                            class="form-control"
                            style="padding-right: 40px; height: 45px; border-radius: 6px; background-color: #fff;"
                            value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }} - {{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}"
                            autocomplete="off"
                        />
                        <span style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%);">
                            <i class="fas fa-calendar-alt text-secondary" style="font-size: 16px;"></i>
                        </span>
                    </div>
                </div>


                <table class="table" id="transaksiTable">
                    <thead>
                        <tr>
                            <th>
                                No
                            </th>
                            <th>
                                Kode Transaksi
                            </th>
                            <th>
                                Nama Agen
                            </th>
                            <th>
                                Jumlah Belanja
                            </th>
                            <th>
                                Total Harga
                            </th>
                            <th>
                                Tanggal
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="modal fade show" id="myModal" tabindex="-1" role="dialog" data-backdrop="static"
            aria-labelledby="roleModalLabel" aria-hidden="true"></div>

        @push('js')
            <!-- Daterangepicker -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
            <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

            <script>
                var dataEvent;
                let start = moment().startOf('month');
                let end = moment().endOf('month');

                // Inisialisasi daterangepicker
                $('#dateRange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    opens: 'left'
                }, function (startDate, endDate) {
                    start = startDate;
                    end = endDate;
                    dataEvent.ajax.reload();
                });

                function modalAction(url = '') {
                    $('#myModal').load(url, function() {
                        $('#myModal').modal('show');
                    });
                }

                $(document).ready(function() {
                    dataEvent = $('#transaksiTable').DataTable({
                        processing: true,
                        serverSide: true,
                        searching: false,
                        lengthChange: false,
                        ajax: {
                            "url": "{{ url('transaksi/list') }}",
                            // "datatypes": "json",
                            "type": "POST",
                            data: function (d) {
                                d.start_date = start.format('YYYY-MM-DD');
                                d.end_date = end.format('YYYY-MM-DD');
                            }
                        },
                        columns: [{
                                data: "DT_RowIndex",
                                name: "DT_RowIndex",
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: "kode_transaksi",
                                className: "",
                                orderable: true,
                                searchable: true,
                            },
                            {
                                data: 'nama_agen',
                                name: 'nama_agen',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'total_qty',
                                name: 'total_qty',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'harga_total',
                                name: 'harga_total',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'tgl_transaksi',
                                name: 'tgl_transaksi',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: "aksi",
                                name: "aksi",
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });
                });

                $('#jenis_event_id').on('change', function() {
                    dataEvent.ajax.reload();
                });

                function searchTable() {
                    var input, filter, table, tr, td, i, j, txtValue;
                    input = document.getElementById("searchInput");
                    filter = input.value.toUpperCase();
                    table = document.getElementById("transaksiTable");
                    tr = table.getElementsByTagName("tr");

                    for (i = 1; i < tr.length; i++) {
                        tr[i].style.display = "none";
                        td = tr[i].getElementsByTagName("td");
                        for (j = 0; j < td.length; j++) {
                            if (td[j]) {
                                txtValue = td[j].textContent || td[j].innerText;
                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                    tr[i].style.display = "";
                                    break;
                                }
                            }
                        }
                    }
                }
            </script>
        </body>

        </html>
    @endpush
@endsection
