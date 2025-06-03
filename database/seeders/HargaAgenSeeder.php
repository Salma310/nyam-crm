<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HargaAgenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 10 agen x 5 barang = 50 kombinasi harga agen
        for ($agenId = 1; $agenId <= 10; $agenId++) {
            for ($barangId = 1; $barangId <= 5; $barangId++) {
                DB::table('m_harga_agen')->insert([
                    'agen_id' => $agenId,
                    'barang_id' => $barangId,
                    'harga' => rand(9000, 15000),
                    'diskon' => rand(0, 1000),
                    'diskon_persen' => rand(0, 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
