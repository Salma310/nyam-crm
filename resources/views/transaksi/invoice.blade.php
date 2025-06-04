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

        .header,
        .footer {
            width: 100%;
        }

        .header img {
            height: 60px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .info,
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .info td {
            vertical-align: top;
            padding: 5px;
        }

        .table th,
        .table td {
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
                <th>Diskon</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi->detailTransaksi as $detail)
                @php
                    $barangId = $detail->barang_id;
                    $hargaInfo = $hargaAgenMap[$barangId] ?? [
                        'harga_satuan' => 0,
                        'totalDiskonItem' => 0,
                        'harga_setelah_diskon' => 0,
                        'harga_final' => 0,
                    ];
                @endphp
                <tr>
                    <td>
                        {{ $detail->barang->nama_barang }}<br>
                        <small>{{ $detail->barang->ukuran }}</small>
                    </td>
                    <td style="text-align: center;">Rp. {{ number_format($hargaInfo['harga_satuan'], 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">{{ $detail->qty }}</td>
                    <td style="text-align: center;">Rp. {{ number_format($hargaInfo['totalDiskonItem'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">Rp. {{ number_format($hargaInfo['harga_final'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach

        </tbody>

        @php
            $diskonTransaksi = $transaksi->diskon_transaksi ?? 0;
            $pajak = $transaksi->pajak_transaksi ?? 0;
            $Gtotal = $subtotal - $diskonTransaksi + $pajak;
        @endphp

        <tfoot>
            <tr class="total-row">
                <td colspan="4">Subtotal</td>
                <td>Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4">Diskon</td>
                <td>Rp. {{ number_format($diskonTransaksi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4">Pajak</td>
                <td>Rp. {{ number_format($pajak, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background:#e7f3fc;">
                <td colspan="4">Total Keseluruhan</td>
                <td>Rp. {{ number_format($Gtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 40px; font-style: italic; font-size: 12px;">* Terima kasih atas pembelian Anda</p>

</body>

</html>
