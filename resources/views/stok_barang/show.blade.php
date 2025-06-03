@empty($barang)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/barang/') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Informasi !!!</h5>
                    Berikut adalah detail data Barang:
                </div>
                <div class="row">
                    <div class="col-md-3 d-flex align-items-center justify-content-center" style="min-height: 250px;">
                        @if ($barang->pic)
                            <img src="{{ asset('uploads/barang/' . $barang->pic) }}" alt="gambar" class="img-fluid"
                                style="max-height: 200px;">
                        @else
                            <p>Tidak ada gambar</p>
                        @endif
                    </div>

                    <div class="col-md-9">
                        <table class="table table-sm table-bordered table-striped mb-0">
                            <tr>
                                <th class="text-right col-3">Kode Barang :</th>
                                <td class="col-9">{{ $barang->kode_barang }}</td>
                            </tr>
                            <tr>
                                <th class="text-right col-3">Nama Barang :</th>
                                <td class="col-9">{{ $barang->nama_barang }}</td>
                            </tr>
                            <tr>
                                <th class="text-right col-3">Kalori :</th>
                                <td class="col-9">{{ $barang->kalori }}</td>
                            </tr>
                            <tr>
                                <th class="text-right col-3">Komposisi :</th>
                                <td class="col-9">{{ $barang->komposisi }}</td>
                            </tr>
                            <tr>
                                <th class="text-right col-3">Kandungan :</th>
                                <td class="col-9">{{ $barang->kandungan }}</td>
                            </tr>
                            <tr>
                                <th class="text-right col-3">Ukuran :</th>
                                <td class="col-9">{{ $barang->ukuran }}</td>
                            </tr>
                            <tr>
                                <th class="text-right col-3">HPP :</th>
                                <td class="col-9">{{ number_format($barang->hpp, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th class="text-right col-3">Stok :</th>
                                <td class="col-9">{{ $barang->stok }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>
                <h5>Histori Stok Keluar</h5>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($histori_keluar as $keluar)
                            <tr>
                                <td>{{ $keluar->transaksi->tgl_transaksi ?? '-' }}</td>
                                <td>{{ $keluar->transaksi->kode_transaksi ?? '-' }}</td>
                                <td>-{{ $keluar->qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <h5>Histori Stok Masuk</h5>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Transaksi Masuk</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($histori_masuk as $masuk)
                            <tr>
                                <td>{{ $masuk->purchase->tgl_transaksi ?? '-' }}</td>
                                <td>{{ $masuk->purchase->kode_transaksi_masuk ?? '-' }}</td>
                                <td>+{{ $masuk->qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Tutup</button>
            </div>
        </div>
    </div>
@endempty
