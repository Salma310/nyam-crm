<style>
    .modal-content {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background-color: #007bff;
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .modal-title {
        font-weight: bold;
    }

    .modal-body {
        padding: 20px;
        background-color: #f9f9f9;
    }

    .form-control {
        border-radius: 8px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .form-group label {
        font-weight: bold;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        transition: background-color 0.3s ease;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .error-text {
        font-size: 0.9rem;
        color: #dc3545;
    }
</style>
@empty($agen)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" arialabel="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/agen') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/agen/' . $agen->agen_id . '/update') }}" method="POST" id="form-edit"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Agen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="overflow-y: auto; max-height:600px; scrollbar-width: thin;">
                    <div class="form-group">
                        <label>Nama Agen</label>
                        <input value="{{ $agen->nama }}" type="text" name="nama" id="nama"
                            class="form-control" required>
                        <small id="error-nama" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input value="{{ $agen->email }}" type="email" name="email" id="email"
                                class="form-control" required>
                            <small id="error-email" class="error-text form-text text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>No Hp</label>
                            <input value="{{ $agen->no_telf }}" type="number" name="no_telf" id="no_telf"
                                class="form-control" required>
                            <small id="error-no_telf" class="error-text form-text text-danger"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input value="{{ $agen->alamat }}" type="text" name="alamat" id="alamat"
                            class="form-control" required>
                        <small id="error-alamat" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input value="{{ $agen->provinsi }}" type="text" name="provinsi" id="provinsi"
                            class="form-control" required>
                        <small id="error-provinsi" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Kota</label>
                            <input value="{{ $agen->kota }}" type="text" name="kota" id="kota"
                                class="form-control" required>
                            <small id="error-kota" class="error-text form-text text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Kecamatan</label>
                            <input value="{{ $agen->kecamatan }}" type="text" name="kecamatan" id="kecamatan"
                                class="form-control" required>
                            <small id="error-kecamatan" class="error-text form-text text-danger"></small>
                        </div>
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
            // Form validation and submission
            $("#form-edit").validate({
                rules: {
                    nama: { required: true, minlength: 3, maxlength: 200 },
                    email: { required: true },
                    no_telf: { required: true, number: true, minlength: 10, maxlength: 15 },
                    alamat: { required: true, minlength: 3, maxlength: 200 },
                    provinsi: { required: true, minlength: 3, maxlength: 100 },
                    kota: { required: true, minlength: 3, maxlength: 100 },
                    kecamatan: { required: true, minlength: 3, maxlength: 100 },                  
                },
                submitHandler: function(form, agen){
                    agen.preventDefault();
                    var formData = new FormData(form);
                    console.log(form);
                    for (var pair of formData.entries()) {
                        console.log(pair[0]+ ', ' + pair[1]); 
                    }

                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                data.ajax.reload();
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
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan saat mengirim data'
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

            // Custom validation method to check date
            $.validator.addMethod("greaterThan",
                function(value, element, params) {
                    return new Date(value) >= new Date($(params).val());
                }
            );
        });
    </script>
@endempty
