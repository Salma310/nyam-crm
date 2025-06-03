<html>
 <head>
      <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .modal-content {
        border-radius: 12px;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        background: linear-gradient(90deg, #007bff, #00c6ff);
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1.3rem;
    }

    .modal-body {
        padding: 25px;
        background-color: #f4f6f9;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px;
    }

    .form-group label {
        font-weight: 600;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .error-text {
        font-size: 0.85rem;
        color: #dc3545;
    }
</style>
</head>
<body>
    

<form action="{{ url('purchase/add') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Pembelian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Jabatan dan Partisipan -->
                <div id="dynamic-fields">
                    <div class="item-row">
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label>Barang</label>
                                <select name="barang[0][barang_id]]" class="form-control barang-select" style="font-family: monospace;" required>
                                    <option value="">Pilih Barang</option>
                                    @foreach($barang as $b)
                                        <option value="{{ $b->barang_id }}">
                                            {{ $b->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Qty</label>
                                <input type="number" name="barang[0][qty]" class="form-control" required>
                            </div>
                             <div class="form-group col-md-1 d-flex align-items-center justify-content-center mt-4">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="form-group col-md-1 d-flex align-items-center justify-content-center mt-4">
                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-item">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Diskon</label>
                        <input type="number" name="diskon_transaksi" id="diskon_transaksi" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Pajak</label>
                        <input type="number" name="pajak_transaksi" id="pajak_transaksi" class="form-control">
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
</body>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        let index = 1;

        $(document).on('click', '#btn-add-item', function () {
            let newField = $('.item-row:first').clone();
            newField.find('select').val('');
            newField.find('input').val('');

            newField.find('[name]').each(function () {
                let name = $(this).attr('name');
                name = name.replace(/\[\d+\]/, `[${index}]`);
                $(this).attr('name', name);
            });

            $('#dynamic-fields').append(newField);
            index++;
        });

        // Event listener for remove buttons (using event delegation)
        $(document).on('click', '.btn-remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
            }
        });
        
        $("#form-tambah").validate({
            rules: {
                kode_transaksi_masuk: {
                    required: true
                },
                "barang[][barang_id]": {
                    required: true
                },
                qty: {
                    required: true
                },
                diskon_transaksi: {
                    required: true
                },
                pajak_transaksi: {
                    required: true
                },
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            });
                            dataEvent.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
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
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
</html>