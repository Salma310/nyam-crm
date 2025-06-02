<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data barang termasuk HPP-nya
        $barangData = DB::table('m_barang')->select('barang_id', 'hpp')->get();

        for ($i = 0; $i < 20; $i++) {
            $jumlahDetail = rand(1, 3); // maksimal 3 barang per transaksi
            $kodeTransaksi = 'TRXMSK' . strtoupper(Str::random(6));
            $tglTransaksi = now()->subDays(rand(0, 30));

            $totalHarga = 0;

            // Insert transaksi_masuk
            $transaksiMasukId = DB::table('t_transaksi_masuk')->insertGetId([
                'kode_transaksi_masuk' => $kodeTransaksi,
                'diskon_transaksi' => 0, // default, bisa diisi jika ingin
                'pajak_transaksi' => 0,   // default, bisa diisi jika ingin
                'harga_total' => 0,       // sementara, akan diupdate nanti
                'tgl_transaksi' => $tglTransaksi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Ambil barang random sebanyak jumlah detail
            $barangDipilih = $barangData->random($jumlahDetail);

            foreach ($barangDipilih as $barang) {
                $qty = rand(1, 5);
                $hargaBarang = $barang->hpp ?? 0;
                $subtotal = $hargaBarang * $qty;

                $totalHarga += $subtotal;

                DB::table('t_detail_transaksi_masuk')->insert([
                    'transaksi_masuk_id' => $transaksiMasukId,
                    'barang_id' => $barang->barang_id,
                    'qty' => $qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update total transaksi
            DB::table('t_transaksi_masuk')->where('transaksi_masuk_id', $transaksiMasukId)->update([
                'harga_total' => $totalHarga,
                'updated_at' => now(),
            ]);
        }
    }
}
