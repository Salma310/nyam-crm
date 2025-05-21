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
                'nama_barang' => 'Bubur Organik Rasa Pisang',
                'kalori' => '120 kkal',
                'komposisi' => 'Beras merah, pisang, air',
                'kandungan' => 'Vitamin B, Zat Besi',
                'ukuran' => '120gr',
                'pic' => 'bubur_pisang.jpg',
                'harga_jual' => 15000,
                'harga_beli' => 10000,
                'stok' => 100
            ],
            [
                'kode_barang' => 'BRG002',
                'nama_barang' => 'Bubur Organik Rasa Apel',
                'kalori' => '130 kkal',
                'komposisi' => 'Beras putih, apel, air',
                'kandungan' => 'Vitamin C, Serat',
                'ukuran' => '120gr',
                'pic' => 'bubur_apel.jpg',
                'harga_jual' => 16000,
                'harga_beli' => 11000,
                'stok' => 80
            ],
            [
                'kode_barang' => 'BRG003',
                'nama_barang' => 'Bubur Instan Sayur Bayam',
                'kalori' => '110 kkal',
                'komposisi' => 'Beras, bayam, air',
                'kandungan' => 'Zat Besi, Kalsium',
                'ukuran' => '100gr',
                'pic' => 'bubur_bayam.jpg',
                'harga_jual' => 14000,
                'harga_beli' => 9500,
                'stok' => 90
            ],
            [
                'kode_barang' => 'BRG004',
                'nama_barang' => 'Bubur Daging Ayam',
                'kalori' => '150 kkal',
                'komposisi' => 'Beras, ayam, kaldu',
                'kandungan' => 'Protein, Zink',
                'ukuran' => '130gr',
                'pic' => 'bubur_ayam.jpg',
                'harga_jual' => 17000,
                'harga_beli' => 12000,
                'stok' => 75
            ],
            [
                'kode_barang' => 'BRG005',
                'nama_barang' => 'Bubur Campur Buah',
                'kalori' => '140 kkal',
                'komposisi' => 'Beras, pisang, apel, air',
                'kandungan' => 'Vitamin A, Vitamin C',
                'ukuran' => '125gr',
                'pic' => 'bubur_campur.jpg',
                'harga_jual' => 16500,
                'harga_beli' => 11500,
                'stok' => 60
            ]
        ]);
    }
}
