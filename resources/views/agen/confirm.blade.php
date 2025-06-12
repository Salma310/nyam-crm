@if (empty($agen))
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/agen') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/agen/' . $agen->agen_id . '/delete') }}" method="POST" id="form-delete-agen">
        @csrf
        @method('DELETE')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Penghapusan Agen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan</h5>
                        Apakah Anda yakin ingin menghapus agen berikut ini?
                    </div>

                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                            <th class="text-right col-3">Nama</th>
                            <td>{{ $agen->nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Email</th>
                            <td>{{ $agen->email }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">No Telepon</th>
                            <td>{{ $agen->no_telf }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Alamat</th>
                            <td>{{ $agen->alamat }}, {{ $agen->kecamatan }}, {{ $agen->kota }}, {{ $agen->provinsi }}
                            </td>
                        </tr>
                    </table>

                    @if ($agen->transaksi->count() > 0 || $agen->hargaAgen->count() > 0)
                        <div class="alert alert-danger mt-3">
                            <h5><i class="icon fas fa-exclamation-circle"></i> Data Terkait Ditemukan</h5>
                            Agen ini memiliki data yang terkait:
                            <ul>
                                @if ($agen->transaksi->count() > 0)
                                    <li><strong>{{ $agen->transaksi->count() }}</strong> Transaksi</li>
                                @endif
                                @if ($agen->hargaAgen->count() > 0)
                                    <li><strong>{{ $agen->hargaAgen->count() }}</strong> Harga Produk</li>
                                @endif
                            </ul>
                            <p>Apakah Anda ingin menghapus seluruh data tersebut beserta agennya?</p>
                            <button id="btn-force-delete" type="button" class="btn btn-warning">
                                Ya, Hapus Semua
                            </button>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-confirm-delete" class="btn btn-primary">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            // Tombol 'Ya, Hapus' (submit biasa)
            $("#form-delete-agen").validate({
                rules: {},
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#agenModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                dataAgen.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error Server',
                                text: 'Terjadi kesalahan saat menghapus agen.'
                            });
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

            // Tombol 'Ya, Hapus Semua' (force delete)
            $('#btn-force-delete').on('click', function() {
                Swal.fire({
                    title: 'Yakin ingin menghapus semua data terkait?',
                    text: "Transaksi dan harga produk juga akan dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus Semua',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/agen/' . $agen->agen_id . '/force-delete') }}",
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    $('#agenModal').modal('hide');
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    });
                                    dataAgen.ajax.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Menghapus',
                                        text: response.message
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus agen.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endif
