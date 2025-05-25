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
                <table class="table table-sm table-bordered table-striped">
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
                        <th class="text-right col-3">PIC :</th>
                        <td class="col-9">{{ $barang->pic }}</td>
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
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Tutup</button>
            </div>
        </div>
    </div>
@endempty
