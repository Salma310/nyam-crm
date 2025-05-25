<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_barang')->insert([
            [
                'kode_barang' => 'BRG001',
                'nama_barang' => 'Puree Apel Organik',
                'kalori' => '80 kkal',
                'komposisi' => 'Apel, Air',
                'kandungan' => 'Vitamin C, Serat',
                'ukuran' => '100gr',
                'pic' => 'puree_apel.jpg',
                'hpp' => 7500.00,
                'stok' => 120,
            ],
            [
                'kode_barang' => 'BRG002',
                'nama_barang' => 'Bubur Bayam',
                'kalori' => '90 kkal',
                'komposisi' => 'Beras Merah, Bayam, Wortel',
                'kandungan' => 'Zat Besi, Vitamin A',
                'ukuran' => '120gr',
                'pic' => 'bubur_bayam.jpg',
                'hpp' => 8000.00,
                'stok' => 85,
            ],
            [
                'kode_barang' => 'BRG003',
                'nama_barang' => 'Snack Pisang Kering',
                'kalori' => '100 kkal',
                'komposisi' => 'Pisang, Minyak Kelapa',
                'kandungan' => 'Kalium, Serat',
                'ukuran' => '50gr',
                'pic' => 'snack_pisang.jpg',
                'hpp' => 5000.00,
                'stok' => 200,
            ],
            [
                'kode_barang' => 'BRG004',
                'nama_barang' => 'Bubur Ayam Kampung',
                'kalori' => '110 kkal',
                'komposisi' => 'Beras, Ayam Kampung, Labu',
                'kandungan' => 'Protein, Zinc',
                'ukuran' => '150gr',
                'pic' => 'bubur_ayam.jpg',
                'hpp' => 9500.00,
                'stok' => 60,
            ],
            [
                'kode_barang' => 'BRG005',
                'nama_barang' => 'Puree Pear dan Oat',
                'kalori' => '85 kkal',
                'komposisi' => 'Pear, Oat, Air',
                'kandungan' => 'Vitamin B, Serat',
                'ukuran' => '100gr',
                'pic' => 'puree_pear.jpg',
                'hpp' => 7800.00,
                'stok' => 95,
            ],
        ]);
    }
}
