@empty($agen)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data agen tidak ditemukan.
                </div>
                <a href="{{ url('/agen') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Agen: {{ $agen->nama }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                {{-- Informasi Agen --}}
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Informasi Agen</h5>
                </div>

                <table class="table table-sm table-bordered">
                    <tr>
                        <th class="text-right col-3">Nama :</th>
                        <td>{{ $agen->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Email :</th>
                        <td>{{ $agen->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">No Telepon :</th>
                        <td>{{ $agen->no_telf }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Alamat :</th>
                        <td>{{ $agen->alamat }}, {{ $agen->kecamatan }}, {{ $agen->kota }}, {{ $agen->provinsi }}</td>
                    </tr>
                </table>

                <hr>
                {{-- Harga Produk Agen --}}
                <h5>Harga Produk untuk Agen</h5>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>HPP</th>
                            <th>Harga</th>
                            <th>Diskon</th>
                            <th>Diskon (%)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                     <tbody>
                        @forelse ($harga_produk as $harga)
                         {{-- @if ($harga->id) --}}
                            {{-- <form action="{{ url('agen/' . $harga->id . '/update_harga') }}" method="POST"> --}}
                            <form action="{{ url('agen/' . ($harga->id ?? 0) . '/update_harga') }}" method="POST">
                            {{-- <form action="{{ route('harga-agen.update', $harga->id) }}" method="POST"> --}}
                                @csrf
                                @method('PUT')
                                <tr>
                                    <td>{{ $harga->barang->nama_barang ?? '-' }}</td>
                                    {{-- <form action="{{ route('harga-agen.update', $harga->id) }}" method="POST"> --}}                                
                                    <td>
                                        <input type="number" name="hpp" class="form-control form-control-sm" value="{{ $harga->barang->hpp }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="harga" class="form-control form-control-sm" value="{{ $harga->harga }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="diskon" class="form-control form-control-sm" value="{{ $harga->diskon }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="diskon_persen" class="form-control form-control-sm" value="{{ $harga->diskon_persen }}" required>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                    </td>
                                </tr>
                            </form>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data harga produk</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <hr>
                {{-- Riwayat Transaksi --}}
                <h5>Riwayat Transaksi Agen</h5>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Transaksi</th>
                                <th>Diskon Transaksi</th>
                                <th>Pajak</th>
                                <th>Harga Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksi as $trx)
                                <tr data-widget="expandable-table" aria-expanded="false">
                                    <td>{{ \Carbon\Carbon::parse($trx->tgl_transaksi)->format('d-m-Y') }}</td>
                                    <td>{{ $trx->kode_transaksi }}</td>
                                    <td>Rp {{ number_format($trx->diskon_transaksi, 0, ',', '.') }}</td>
                                    <td>{{ $trx->pajak_transaksi }}%</td>
                                    <td>Rp {{ number_format($trx->harga_total, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="expandable-body d-none">
                                    <td colspan="5">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Nama Barang</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($trx->detailTransaksi as $detail)
                                                    <tr>
                                                        <td>{{ $detail->barang->nama_barang }}</td>
                                                        <td>{{ $detail->qty }}</td>
                                                        @php
                                                            $hargaAgen = $harga_produk->firstWhere('barang_id', $detail->barang_id);
                                                        @endphp
                                                        <td>
                                                            @if($hargaAgen)
                                                                Rp {{ number_format($hargaAgen->harga, 0, ',', '.') }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data transaksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>

            <div class="modal-footer">
                <a href="{{ url('/agen') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-widget="expandable-table"]').forEach(function(row) {
            row.addEventListener('click', function() {
                const next = row.nextElementSibling;
                if (next && next.classList.contains('expandable-body')) {
                    next.classList.toggle('d-none');
                }
            });
        });
    </script>

@endempty

{{-- @empty($agen)
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
@endempty --}}
