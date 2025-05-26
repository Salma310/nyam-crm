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
            $pajakTotal = 0;
            $diskonTotal = 0;
   
            $transaksiId = DB::table('t_transaksi')->insertGetId([
                'kode_transaksi' => $kodeTransaksi,
                'agen_id' => $agenId,
                'diskon' => 0, // akan dihitung dari detail
                'pajak' => 0, // akan dihitung dari detail
                'harga_total' => 0, // akan diupdate
                'tgl_transaksi' => $tglTransaksi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $barangDipilih = collect($barangIds)->random($jumlahDetail);
            foreach ($barangDipilih as $barangId) {
                $qty = rand(1, 5);

                // Ambil harga dari m_harga_agen
                $hargaRow = $hargaData->first(function ($row) use ($agenId, $barangId) {
                    return $row->agen_id == $agenId && $row->barang_id == $barangId;
                });

                if (!$hargaRow) continue;

                $hargaBarang = $hargaRow->harga;
                // $diskon1 = $hargaRow->diskon;
                // $diskon2 = $hargaRow->diskon_2;
                // $pajak = $hargaRow->pajak;

                // $hargaSetelahDiskon = $hargaBarang * (1 - ($diskon1 + $diskon2) / 100);
                // $hargaAkhir = ($hargaSetelahDiskon + $pajak) * $qty;

                $hargaAkhir = $hargaBarang * $qty;
                $totalHarga += $hargaAkhir;

                // $diskonTotal += ($hargaBarang * ($diskon1 + $diskon2) / 100) * $qty;
                // $pajakTotal += $pajak * $qty;

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
                    'diskon' => $diskonTotal,
                    'pajak' => $pajakTotal,
                    'updated_at' => now(),
                ]);
        }
    }
    // public function run(): void
    //  {
    //     $barangs = [
    //         ['id' => 1, 'harga_jual' => 15000],
    //         ['id' => 2, 'harga_jual' => 16000],
    //         ['id' => 3, 'harga_jual' => 14000],
    //         ['id' => 4, 'harga_jual' => 17000],
    //         ['id' => 5, 'harga_jual' => 16500],
    //     ];

    //     for ($i = 1; $i <= 20; $i++) {
    //         $kodeTransaksi = 'TRX' . str_pad($i, 4, '0', STR_PAD_LEFT);
    //         $agenId = rand(1, 10);
    //         $tglTransaksi = Carbon::now()->subDays(rand(0, 30))->toDateString();
    //         $detailItems = [];

    //         // Buat 1–3 detail transaksi per transaksi
    //         $jumlahDetail = rand(1, 3);
    //         $hargaTotal = 0;

    //         for ($j = 0; $j < $jumlahDetail; $j++) {
    //             $barang = $barangs[array_rand($barangs)];
    //             $qty = rand(1, 5);
    //             $subTotal = $barang['harga_jual'] * $qty;
    //             $hargaTotal += $subTotal;

    //             $detailItems[] = [
    //                 'barang_id' => $barang['id'],
    //                 'qty' => $qty
    //             ];
    //         }

    //         // Simpan transaksi
    //         $transaksiId = DB::table('t_transaksi')->insertGetId([
    //             'kode_transaksi' => $kodeTransaksi,
    //             'agen_id' => $agenId,
    //             'harga_total' => $hargaTotal,
    //             'tgl_transaksi' => $tglTransaksi,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         // Simpan detail transaksi
    //         foreach ($detailItems as $item) {
    //             DB::table('t_detail_transaksi')->insert([
    //                 'transaksi_id' => $transaksiId,
    //                 'barang_id' => $item['barang_id'],
    //                 'qty' => $item['qty'],
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }
    //     }
    // }
}
