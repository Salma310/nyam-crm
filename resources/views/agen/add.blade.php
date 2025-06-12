<style>
    .modal-content {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        /* background-color: #28a745; */
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

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .error-text {
        font-size: 0.9rem;
        color: #dc3545;
    }
</style>
<form action="{{ url('agen/add') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #007bff">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Agen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                    <small id="error-nama" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <small id="error-email" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>No Hp</label>
                    <input type="number" name="no_telf" id="no_telf" class="form-control" required>
                    <small id="error-no_telf" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" id="alamat" class="form-control" required>
                    <small id="error-alamat" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Provinsi</label>
                    <input type="text" name="provinsi" id="provinsi" class="form-control" required>
                    <small id="error-provinsi" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="kota" id="kota" class="form-control" required>
                    <small id="error-kota" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Kecamatan</label>
                    <input type="text" name="kecamatan" id="kecamatan" class="form-control" required>
                    <small id="error-kecamatan" class="error-text form-text text-danger"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
$(document).ready(function() {
    $("#form-tambah").validate({
        rules: {
            nama: { required: true, minlength: 3, maxlength: 200 },
            email: { required: true },
            no_telf: { required: true, number: true, minlength: 10, maxlength: 15 },
            alamat: { required: true, minlength: 3, maxlength: 200 },
            provinsi: { required: true, minlength: 3, maxlength: 100 },
            kota: { required: true, minlength: 3, maxlength: 100 },
            kecamatan: { required: true, minlength: 3, maxlength: 100 },
        },
        submitHandler: function(form) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                success: function(response) {
                    if(response.status){
                        $('#agenModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        dataAgen.ajax.reload();
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
