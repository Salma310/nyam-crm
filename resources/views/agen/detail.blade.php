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
                            <tr>
                                <td>{{ $harga->barang->nama_barang ?? '-' }}</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm"
                                        value="{{ $harga->barang->hpp }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm input-harga" name="harga"
                                        value="{{ $harga->harga }}" data-id="{{ $harga->harga_agen_id }}" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm input-diskon" name="diskon"
                                        value="{{ $harga->diskon }}" data-id="{{ $harga->harga_agen_id }}" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm input-diskon-persen"
                                        name="diskon_persen" value="{{ $harga->diskon_persen }}"
                                        data-id="{{ $harga->harga_agen_id }}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary btn-simpan-harga"
                                        data-id="{{ $harga->harga_agen_id }}">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data harga produk</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div id="notif-area"></div> <!-- Tempat menampilkan feedback -->

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
                                            @foreach ($trx->detailTransaksi as $detail)
                                                <tr>
                                                    <td>{{ $detail->barang->nama_barang }}</td>
                                                    <td>{{ $detail->qty }}</td>
                                                    @php
                                                        $hargaAgen = $harga_produk->firstWhere(
                                                            'barang_id',
                                                            $detail->barang_id,
                                                        );
                                                    @endphp
                                                    <td>
                                                        @if ($hargaAgen)
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

        document.querySelectorAll('.btn-simpan-harga').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = button.dataset.id;

                const harga = document.querySelector(`input[name="harga"][data-id="${id}"]`).value;
                const diskon = document.querySelector(`input[name="diskon"][data-id="${id}"]`).value;
                const diskon_persen = document.querySelector(`input[name="diskon_persen"][data-id="${id}"]`)
                    .value;

                fetch(`/agen/${id}/update_harga`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            harga: harga,
                            diskon: diskon,
                            diskon_persen: diskon_persen
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        let notif = document.getElementById('notif-area');
                        notif.innerHTML = '';
                        const alertBox = document.createElement('div');
                        alertBox.className = `alert ${data.status ? 'alert-success' : 'alert-danger'}`;
                        alertBox.innerText = data.message;
                        notif.appendChild(alertBox);

                        setTimeout(() => notif.innerHTML = '', 3000);
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Terjadi kesalahan saat menyimpan data.");
                    });
            });
        });
    </script>

@endempty
