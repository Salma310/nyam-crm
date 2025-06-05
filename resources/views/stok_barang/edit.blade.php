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
                <a href="{{ url('/barang') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/barang/' . $barang->barang_id . '/update') }}" method="POST" id="form-edit-barang"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input value="{{ $barang->nama_barang }}" type="text" name="nama_barang" id="nama_barang"
                            class="form-control" required>
                        <small id="error-nama_barang" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Kalori</label>
                        <input value="{{ $barang->kalori }}" type="text" name="kalori" id="kalori"
                            class="form-control" required>
                        <small id="error-kalori" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Komposisi</label>
                        <input value="{{ $barang->komposisi }}" type="text" name="komposisi" id="komposisi"
                            class="form-control" required>
                        <small id="error-komposisi" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Kandungan</label>
                        <input value="{{ $barang->kandungan }}" type="text" name="kandungan" id="kandungan"
                            class="form-control" required>
                        <small id="error-kandungan" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Ukuran</label>
                        <input value="{{ $barang->ukuran }}" type="text" name="ukuran" id="ukuran"
                            class="form-control" required>
                        <small id="error-ukuran" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Gambar (PIC)</label><br>
                        @if ($barang->pic)
                            <img src="{{ asset('uploads/barang/' . $barang->pic) }}" alt="gambar" class="mb-2"
                                width="100">
                        @endif
                        <input type="file" name="pic" id="pic" class="form-control">
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                        <small id="error-pic" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>HPP</label>
                        <input value="{{ $barang->hpp }}" type="number" name="hpp" id="hpp" class="form-control"
                            required>
                        <small id="error-hpp" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input value="{{ $barang->stok }}" type="number" name="stok" id="stok" class="form-control" readonly
                            required>
                        <small id="error-stok" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $("#form-edit-barang").validate({
                rules: {
                    nama_barang: {
                        required: true
                    },
                    kalori: {
                        required: true
                    },
                    komposisi: {
                        required: true
                    },
                    kandungan: {
                        required: true
                    },
                    ukuran: {
                        required: true
                    },
                    pic: {
                        required: true
                    },
                    stok: {
                        required: true,
                        digits: true
                    },
                    hpp: {
                        required: true,
                        number: true
                    },
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                $('#barangModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                dataBarang.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty
