<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agen;

class AgenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'agen_id' => '1',
                'nama' => 'Ahmad Ramadhan',
                'email' => 'ahmad.ramadhan@gmail.com',
                'no_telf' => '6281234567890',
                'alamat' => 'Jl. Ikan Mas No. 23',
                'kecamatan' => 'Beji',
                'kota' => 'Depok',
                'provinsi' => 'Jawa Barat',
            ],
            [
                'agen_id' => '2',
                'nama' => 'Rina Maharani',
                'email' => 'rina.maharani@yahoo.com',
                'no_telf' => '6282145678912',
                'alamat' => 'Jl. Semanggi No. 15',
                'kecamatan' => 'Cipayung',
                'kota' => 'Jakarta Timur',
                'provinsi' => 'DKI Jakarta',
            ],
            [
                'agen_id' => '3',
                'nama' => 'Dedi Santoso',
                'email' => 'dedi.santoso@gmail.com',
                'no_telf' => '6285212345678',
                'alamat' => 'Jl. Letjen Sutoyo No. 78',
                'kecamatan' => 'Kebayoran Baru',
                'kota' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
            ],
            [
                'agen_id' => '4',
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@gmail.com',
                'no_telf' => '6289876543210',
                'alamat' => 'Jl. Soekarno Hatta No. 12',
                'kecamatan' => 'Tegalsari',
                'kota' => 'Surabaya',
                'provinsi' => 'Jawa Timur',
            ],
            [
                'agen_id' => '5',
                'nama' => 'Bayu Pratama',
                'email' => 'bayu.pratama@gmail.com',
                'no_telf' => '6287712345678',
                'alamat' => 'Jl. Tlogomas No. 10',
                'kecamatan' => 'Baturetno',
                'kota' => 'Wonogiri',
                'provinsi' => 'Jawa Tengah',
            ],
            [
                'agen_id' => '6',
                'nama' => 'Lilis Handayani',
                'email' => 'lilis.handayani@gmail.com',
                'no_telf' => '6281345678921',
                'alamat' => 'Jl. Veteran No. 30',
                'kecamatan' => 'Tegalrejo',
                'kota' => 'Yogyakarta',
                'provinsi' => 'DI Yogyakarta',
            ],
            [
                'agen_id' => '7',
                'nama' => 'Fajar Hidayat',
                'email' => 'fajar.hidayat@gmail.com',
                'no_telf' => '6282334455667',
                'alamat' => 'Jl. Bandung No. 99',
                'kecamatan' => 'Cicendo',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
            ],
            [
                'agen_id' => '8',
                'nama' => 'Nadia Amalia',
                'email' => 'nadia.amalia@gmail.com',
                'no_telf' => '6283812345678',
                'alamat' => 'Jl. Jakarta No. 55',
                'kecamatan' => 'Cibeunying Kaler',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
            ],
            [
                'agen_id' => '9',
                'nama' => 'Yoga Permana',
                'email' => 'yoga.permana@gmail.com',
                'no_telf' => '6289912345678',
                'alamat' => 'Jl. Serang Baru No. 8',
                'kecamatan' => 'Serang',
                'kota' => 'Serang',
                'provinsi' => 'Banten',
            ],
            [
                'agen_id' => '10',
                'nama' => 'Desi Ratnasari',
                'email' => 'desi.ratnasari@gmail.com',
                'no_telf' => '6288812345612',
                'alamat' => 'Jl. Kartini No. 31',
                'kecamatan' => 'Purwokerto Selatan',
                'kota' => 'Purwokerto',
                'provinsi' => 'Jawa Tengah',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Agen::create($item);
        }
    }
}
