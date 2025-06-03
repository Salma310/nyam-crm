<div id="modal-master" class="modal-dialog modal-xl" role="document">
    <div class="modal-content p-4">
        <div class="modal-header">
            <h2>Detail Pembelian</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
    <div class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            {{-- Kiri --}}
            <div>
                <p><strong>Kode Pembelian:</strong> {{ $purchase->kode_transaksi_masuk }}</p>
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($purchase->tgl_transaksi)->format('d-m-Y') }}</p>
                <p><strong>Total Harga:</strong> Rp {{ number_format($purchase->harga_total, 0, ',', '.') }}</p>
            </div> 

            {{-- Kanan --}}
            <div class="text-end">
                <a href="{{ url('purchase/' . $purchase->transaksi_masuk_id . '/print') }}" target="_blank"
                    class="btn btn-primary btn-sm" title="Cetak Invoice">
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
                            <th>Harga</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $subtotal = 0;
                            $grandTotal = 0;
                        @endphp
                        @foreach ($detailHarga as $item)
                            @php
                                $detail = $item['detail'];
                                $qty = $detail->qty;
                                $hpp = $item['hpp'];
                                $subtotal = $item['subtotal'];
                                $hargaSatuan = $item['harga_satuan'];
                            @endphp
                            <tr>
                                <td>{{ $detail->barang->kode_barang ?? '-' }}</td>
                                <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $qty }}</td>
                                <td>Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3"><strong>Diskon</strong></td>
                            <td colspan="2"><strong>Rp. {{ number_format($purchase->diskon_transaksi, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3"><strong>Pajak</strong></td>
                            <td colspan="2"><strong>Rp. {{ number_format($purchase->pajak_transaksi, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr class="total-row" style="background:#e7f3fc;">
                            <td colspan="3"><strong>Total Penjualan</strong></td>
                            <td colspan="2"><strong>Rp. {{ number_format($purchase->harga_total, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>