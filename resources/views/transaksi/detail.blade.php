<div id="modal-master" class="modal-dialog modal-xl" role="document">
    <div class="modal-content p-4">
        <div class="modal-header">
            <h2>Detail Transaksi</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="container mt-4">
            <div class="d-flex justify-content-between mb-3">
                {{-- Kiri --}}
                <div>
                    <h4><strong>Data Agen</strong></h4>
                    <p><strong>Nama:</strong> {{ $transaksi->agen->nama ?? '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $transaksi->agen->alamat ?? '-' }},
                        {{ $transaksi->agen->kecamatan ?? '-' }}</p>
                    <p><strong>Kota:</strong> {{ $transaksi->agen->kota ?? '-' }}</p>
                    <p><strong>Provinsi:</strong> {{ $transaksi->agen->provinsi ?? '-' }}</p>
                    <p><strong>No. Telp:</strong> {{ $transaksi->agen->no_telf ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $transaksi->agen->email ?? '-' }}</p>
                </div>

                {{-- Kanan --}}
                <div class="text-end">
                    <p><strong>Kode Transaksi:</strong> {{ $transaksi->kode_transaksi }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaksi->tgl_transaksi)->format('d-m-Y') }}
                    </p>
                    <p><strong>Total Harga:</strong> Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>
                    <div class="btn-group" role="group" aria-label="Invoice Actions">
                        <a href="{{ url('transaksi/' . $transaksi->transaksi_id . '/print') }}" target="_blank"
                            class="btn btn-outline-secondary btn-sm" title="Cetak Invoice">
                            <i class="fas fa-print"></i> Print
                        </a>

                        <button type="button" class="btn btn-outline-success btn-sm btn-send-wa"
                            data-id="{{ $transaksi->transaksi_id }}">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <i class="fab fa-whatsapp"></i> <span class="btn-text">WhatsApp</span>
                        </button>

                        <button type="button" class="btn btn-outline-info btn-sm btn-send-email"
                            data-id="{{ $transaksi->transaksi_id }}" title="Kirim via Email">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <i class="fas fa-envelope"></i> <span class="btn-text">Email</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabel Detail Barang --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Daftar Barang</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>Harga Jual</th>
                                    <th>Diskon</th>
                                    <th>Total Harga</th>
                                    <th>Hpp</th>
                                    <th>Keuntungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $subtotal = 0;
                                    $totalKeuntungan = 0;
                                @endphp
                                @foreach ($detailHarga as $item)
                                    @php
                                        $detail = $item['detail'];
                                        $qty = $detail->qty;
                                        $hpp = $item['hpp'];
                                        $totalBeli = $hpp * $qty;
                                        $keuntungan = $item['harga_final'] - $totalBeli;
                                        $totalKeuntungan += $keuntungan;
                                        $totalSemua =
                                            $totalHarga + $transaksi->pajak_transaksi - $transaksi->diskon_transaksi;
                                    @endphp
                                    <tr>
                                        <td>{{ $detail->barang->kode_barang ?? '-' }}</td>
                                        <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                        <td>{{ $qty }}</td>
                                        <td>Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item['diskon'], 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item['harga_final'], 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($keuntungan, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="6"><strong>Diskon</strong></td>
                                    <td colspan="3"><strong>Rp.
                                            {{ number_format($transaksi->diskon_transaksi, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="6"><strong>Pajak</strong></td>
                                    <td colspan="3"><strong>Rp.
                                            {{ number_format($transaksi->pajak_transaksi, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr class="total-row" style="background:#e7f3fc;">
                                    <td colspan="6"><strong>Total Penjualan</strong></td>
                                    <td colspan="3"><strong>Rp.
                                            {{ number_format($totalSemua, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="6"><strong>Total Keuntungan</strong></td>
                                    <td colspan="3"><strong>Rp.
                                            {{ number_format($totalKeuntungan, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelector('.btn-send-email').addEventListener('click', function() {
        const btn = this;
        const transaksiId = btn.getAttribute('data-id');
        const spinner = btn.querySelector('.spinner-border');
        const btnText = btn.querySelector('.btn-text');

        Swal.fire({
            title: 'Kirim Invoice?',
            text: "Invoice akan dikirim ke email agen.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable tombol dan tampilkan spinner
                btn.disabled = true;
                spinner.classList.remove('d-none');
                btnText.textContent = ' Mengirim...';

                // Tampilkan loading swal
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Harap tunggu sementara sistem mengirimkan email.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/transaksi/${transaksiId}/sendByEmail`, {
                        method: 'GET'
                    })
                    .then(response => {
                        if (!response.ok) throw new Error("Gagal mengirim email");
                        return response.text();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Email berhasil dikirim.'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message || 'Terjadi kesalahan saat mengirim email.'
                        });
                    })
                    .finally(() => {
                        // Aktifkan tombol kembali dan sembunyikan spinner
                        btn.disabled = false;
                        spinner.classList.add('d-none');
                        btnText.textContent = ' Send Email';
                    });
            }
        });
    });

    $('.btn-send-wa').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        Swal.fire({
            title: 'Kirim WhatsApp?',
            text: "Pastikan data sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                // Ubah tombol menjadi loading
                const spinner = btn.find('.spinner-border');
                const btnText = btn.find('.btn-text');
                spinner.removeClass('d-none');
                btnText.text('Mengirim...');

                // Tampilkan loading SweetAlert
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu beberapa detik.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kirim permintaan ke server
                $.ajax({
                    url: `/transaksi/${id}/send`,
                    method: 'GET',
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: res.message ?? 'Berhasil mengirim WhatsApp.'
                        });
                    },
                    error: function(xhr) {
                        let errorText = 'Terjadi kesalahan.';

                        if (xhr.responseJSON?.message) {
                            errorText = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorText = xhr.responseText;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorText
                        });
                    },
                    complete: function() {
                        // Kembalikan tombol ke keadaan semula
                        spinner.addClass('d-none');
                        btnText.text('WhatsApp');
                    }
                });
            }
        });
    });
</script>
