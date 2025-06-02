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
                <th>Diskon</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
         {{-- @php
            $subtotal = 0;
            $totalPajak = 0;
        @endphp

        @foreach($transaksi->detailTransaksi as $detail)
            @php
                $barangId = $detail->barang_id;
                $qty = $detail->qty;

                // Ambil harga agen sesuai barang
                $hargaData = $hargaAgen[$barangId] ?? null;

                $harga = $hargaData->harga ?? 0;
                $diskon1 = $hargaData->diskon ?? 0; // rupiah
                // $diskon2 = $hargaData->diskon_2 ?? 0; // persen
                $pajakPersen = $hargaData->pajak ?? 0; // persen

                // Hitung harga setelah diskon 1 (rupiah)
                $hargaSetelahDiskon1 = $harga - $diskon1;

                // Hitung diskon 2 (persen)
                // $diskonPersenValue = ($hargaSetelahDiskon1 * $diskon2) / 100;
                // $hargaSetelahDiskon2 = $hargaSetelahDiskon1 - $diskonPersenValue;

                // Hitung pajak (persen)
                $pajakValue = ($hargaSetelahDiskon1 * $pajakPersen) / 100;

                // Harga akhir per item
                $hargaFinal = $hargaSetelahDiskon1 + $pajakValue;

                // Total untuk baris ini
                $total = $hargaFinal * $qty;

                // Tambahkan ke totalan
                // $subtotal += $total;
                // $totalPajak += $pajakValue * $qty;
            @endphp

            <tr>
                <td>
                    {{ $detail->barang->nama_barang }}<br>
                    <small>{{ $detail->barang->ukuran }}</small>
                </td>
                <td style="text-align: center;">Rp. {{ number_format($hargaData, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $qty }}</td>
                <td style="text-align: center;">
                    Rp. {{ number_format($diskon1, 0, ',', '.') }} 
                    {{-- + {{ $diskon2 }}% 
                </td>
                <td style="text-align: right;">Rp. {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        @endforeach

        @php
            $diskonTransaksi = $transaksi->diskon ?? 0;   
            $grandTotal = $subtotal - $diskonTransaksi;
        @endphp

        <tfoot>
            <tr class="total-row">
                <td colspan="4">Subtotal</td>
                <td style="text-align: right;">Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4">Diskon Transaksi</td>
                <td style="text-align: right;">Rp. {{ number_format($diskonTransaksi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4">Pajak Total</td>
                <td style="text-align: right;">Rp. {{ number_format($pajakValue, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background:#e7f3fc;">
                <td colspan="4"><strong>Total Keseluruhan</strong></td>
                <td style="text-align: right;"><strong>Rp. {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot> --}}
   
        @php
            $subtotal = 0;
        @endphp
        @foreach($transaksi->detailTransaksi as $detail)
            @php
                $hargaJual = $detail->barang->harga_jual ?? 0;
                $diskon = $detail->diskon ?? 0;
                if($diskon){
                    $hargaJual -= $diskon;
                }
                $total = $detail->qty * $hargaJual;
                $subtotal += $total;

            @endphp
            <tr>
                <td>
                    {{ $detail->barang->nama_barang }}<br>
                    <small>{{ $detail->barang->ukuran }}</small>
                </td>
                <td style="text-align: center;">Rp. {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
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
                <td colspan="3">Diskon</td>
                <td>Rp. {{ number_format($transaksi->diskon ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3">Pajak</td>
                <td>Rp. {{ number_format(0, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background:#e7f3fc;">
                <td colspan="3">Total Keseluruhan</td>
                <td>Rp. {{ number_format($Gtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 40px; font-style: italic; font-size: 12px;">* Terima kasih atas pembelian Anda</p>

</body>
</html>