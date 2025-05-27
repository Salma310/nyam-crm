@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container mt-4">
    <h2>Detail Transaksi</h2>
    <div class="d-flex justify-content-between mb-3">
        {{-- Kiri --}}
        <div>
            <h5>Data Agen</h5>
            <p><strong>Nama:</strong> {{ $transaksi->agen->nama ?? '-' }}</p>
            <p><strong>Alamat:</strong> {{ $transaksi->agen->alamat ?? '-' }}</p>
            <p><strong>Kecamatan:</strong> {{ $transaksi->agen->kecamatan ?? '-' }}</p>
            <p><strong>Kota:</strong> {{ $transaksi->agen->kota ?? '-' }}</p>
            <p><strong>Provinsi:</strong> {{ $transaksi->agen->provinsi ?? '-' }}</p>
            <p><strong>No. Telp:</strong> {{ $transaksi->agen->no_telf ?? '-' }}</p>
        </div>

        {{-- Kanan --}}
        <div class="text-end">
            <h5>Detail Transaksi</h5>
            <p><strong>Kode Transaksi:</strong> {{ $transaksi->kode_transaksi }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaksi->tgl_transaksi)->format('d-m-Y') }}</p>
            <p><strong>Total Harga:</strong> Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>
            <a href="{{ url('transaksi/' . $transaksi->transaksi_id . '/print') }}" target="_blank" class="btn btn-primary btn-sm" title="Cetak Invoice">
                <i class="fas fa-print"></i> Print
            </a>
        </div>
    </div>

    {{-- Tabel Detail Barang --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Daftar Barang</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga Jual</th>
                        <th>Diskon</th>
                        <th>Pajak</th>
                        <th>Total Harga</th>
                        <th>Hpp</th>
                        <th>Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotal = 0;
                        $totalKeuntungan = 0;
                    @endphp
                    @foreach ($transaksi->detailTransaksi as $detail)
                    @php
                        // $hargaJual = $detail->hargaAgen->harga ?? 0;
                        $hpp = $detail->barang->hpp ?? 0;
                        $qty = $detail->qty;

                        $totalJual = $hgAgen * $qty;
                        $totalBeli = $hpp * $qty;
                        $keuntungan = $totalJual - $totalBeli;

                        if ($transaksi->diskon) {
                            $totalJual -= $transaksi->diskon;
                        } 
                        if ($transaksi->pajak) {
                            $totalJual += $transaksi->pajak;
                        }

                        $subtotal += $totalJual;
                        $totalKeuntungan += $keuntungan;
                    @endphp
                    <tr>
                        <td>{{ $detail->barang->kode_barang ?? '-' }}</td>
                        <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $qty }}</td>
                        <td>Rp {{ number_format($hgAgen, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($transaksi->pajak, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totalJual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($keuntungan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row" style="background:#e7f3fc;">
                        <td colspan="6"><strong>Total Penjualan</strong></td>
                        <td colspan="3"><strong>Rp. {{ number_format($totalHarga, 0, ',', '.') }}</strong></td>
                    </tr>   
                     <tr class="total-row">
                        <td colspan="6"><strong>Total Keuntungan</strong></td>
                        <td colspan="3"><strong>Rp. {{ number_format($totalKeuntungan, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <a href="{{ url('/transaksi') }}" class="btn btn-warning">Kembali</a>
</div>
@endsection
