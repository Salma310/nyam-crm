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

<form action="{{ url('barang/add') }}" method="POST" id="form-tambah" enctype="multipart/form-data">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                    $fields = [
                        'kode_barang' => 'Kode Barang',
                        'nama_barang' => 'Nama Barang',
                        'kalori' => 'Kalori',
                        'komposisi' => 'Komposisi',
                        'kandungan' => 'Kandungan',
                        'ukuran' => 'Ukuran',
                        'pic' => 'PIC',
                        // 'stok' => 'Stok',
                        'hpp' => 'HPP',
                    ];

                    $placeholders = [
                        'kode_barang' => 'Contoh: BRG001',
                        'nama_barang' => 'Contoh: Puree Apel Organik',
                        'kalori' => 'Contoh: 80 kkal',
                        'komposisi' => 'Contoh: Apel, Air',
                        'kandungan' => 'Contoh: Vitamin C, Serat',
                        'ukuran' => 'Contoh: 100gr',
                        'pic' => 'Unggah gambar produk (jpg/jpeg/png)',
                        // 'stok' => 'Contoh: 120',
                        'hpp' => 'Contoh: 7500.00',
                    ];
                @endphp

                @foreach ($fields as $field => $label)
                    <div class="form-group">
                        <label>{{ $label }}</label>
                        <input
                            type="{{ in_array($field, ['stok', 'hpp']) ? 'number' : ($field === 'pic' ? 'file' : 'text') }}"
                            name="{{ $field }}" id="{{ $field }}" class="form-control"
                            placeholder="{{ $field !== 'pic' ? $placeholders[$field] : '' }}"
                            {{ $field === 'pic' ? '' : 'required' }}>
                            {{-- {{ $field === 'stok' ? 'readonly' : '' }} --}}
                        <small class="form-text text-muted">{{ $placeholders[$field] }}</small>
                        <small id="error-{{ $field }}" class="error-text form-text text-danger"></small>
                    </div>
                @endforeach
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
                kode_barang: {
                    required: true
                },
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
                    required: false,
                    digits: true
                },
                hpp: {
                    required: true,
                    number: true
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status) {
                            $('#barangModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            });
                            dataBarang.ajax.reload();
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
