@php
    use Carbon\Carbon;

@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
        }
        .header, .footer { width: 100%; }
        .header img { height: 60px; }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 0;
        }
        .info, .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .info td {
            vertical-align: top;
            padding: 5px;
        }
        .table th, .table td {
            border-bottom: 1px solid #ccc;
            padding: 8px;
        }
        .table th {
            background: #e7f3fc;
        }
        .total-row td {
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- Logo & Judul --}}
    <table class="header">
        <tr>
            <td><img src="{{ public_path('logo.png') }}" alt="Logo"></td>
            <td style="text-align: right;">
                <div class="title">INVOICE</div>
                <div>No: {{ $transaksi->kode_transaksi }}</div>
                <div>Tanggal: {{ Carbon::parse($transaksi->tgl_transaksi)->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- Info Agen & Perusahaan --}}
    <table class="info">
        <tr>
            <td>
                <strong>Kepada:</strong><br>
                {{ $transaksi->agen->nama }}<br>
                {{ $transaksi->agen->no_telf }}<br>
                {{ $transaksi->agen->alamat }}, {{ $transaksi->agen->kecamatan }}<br>
                {{ $transaksi->agen->kota }}, {{ $transaksi->agen->provinsi }}
            </td>
        </tr>
    </table>

    {{-- Tabel Barang --}}
    <table class="table">
        <thead>
            <tr>
                <th>Deskripsi Barang</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        @php
            $subtotal = 0;
        @endphp
        @foreach($transaksi->detailTransaksi as $detail)
            @php
                $total = $detail->qty * $detail->barang->harga_jual;
                $subtotal += $total;
            @endphp
            <tr>
                <td>
                    {{ $detail->barang->nama_barang }}<br>
                    <small>{{ $detail->barang->ukuran }}</small>
                </td>
                <td style="text-align: center;">Rp. {{ number_format($detail->barang->harga_jual, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $detail->qty }}</td>
                <td style="text-align: center;">Rp. {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3">Pajak</td>
                {{-- <td>Rp. {{ number_format($subtotal * 0.1, 0, ',', '.') }}</td> --}}
                <td>Rp. {{ number_format(0, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background:#e7f3fc;">
                <td colspan="3">Total Keseluruhan</td>
                <td>Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 40px; font-style: italic; font-size: 12px;">* Terima kasih atas pembelian Anda</p>

</body>
</html>