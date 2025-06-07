<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     public function run(): void
    {
        $barangIds = DB::table('m_barang')->pluck('barang_id')->toArray();
        $agenIds = DB::table('m_agen')->pluck('agen_id')->toArray();
        $hargaData = DB::table('m_harga_agen')->get();

        for ($i = 0; $i < 20; $i++) { // buat 20 transaksi
            $agenId = $agenIds[array_rand($agenIds)];
            $jumlahDetail = rand(1, 3); // maksimal 3 barang per transaksi

            $kodeTransaksi = 'TRX' . strtoupper(Str::random(6));
            $tglTransaksi = now()->subDays(rand(0, 30));

            $totalHarga = 0;
            $diskonTotal = 0;
   
            $transaksiId = DB::table('t_transaksi')->insertGetId([
                'kode_transaksi' => $kodeTransaksi,
                'agen_id' => $agenId,
                'diskon_transaksi' => 0, // akan dihitung dari detail
                'pajak_transaksi' => 0, // akan dihitung dari detail
                'harga_total' => 0, // akan diupdate
                'tgl_transaksi' => $tglTransaksi,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'transaksi_id');

            $barangDipilih = collect($barangIds)->random($jumlahDetail);
            foreach ($barangDipilih as $barangId) {
                $qty = rand(1, 5);

                // Ambil harga dari m_harga_agen
                $hargaRow = $hargaData->first(function ($row) use ($agenId, $barangId) {
                    return $row->agen_id == $agenId && $row->barang_id == $barangId;
                });

                if (!$hargaRow) continue;

                $hargaBarang = $hargaRow->harga;
                $diskon1 = $hargaRow->diskon;
                $diskon2 = $hargaRow->diskon_persen;

                $hargaSetelahDiskon = $hargaBarang - ($diskon1 + (($diskon2 * $hargaBarang) / 100));
                $hargaAkhir = $hargaSetelahDiskon * $qty;

                $totalHarga += $hargaAkhir;

                DB::table('t_detail_transaksi')->insert([
                    'transaksi_id' => $transaksiId,
                    'barang_id' => $barangId,
                    'qty' => $qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update total transaksi setelah detail dibuat
            DB::table('t_transaksi')
                ->where('transaksi_id', $transaksiId)
                ->update([
                    'harga_total' => $totalHarga,
                    'diskon_transaksi' => $diskonTotal,
                    'updated_at' => now(),
                ]);
        }
    }
}
