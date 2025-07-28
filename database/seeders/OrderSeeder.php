<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('account')->insert([
            [
                'id_account' => 'AC001',
                'username' => 'jason',
                'email' => 'jasoncliendo@gmail.com',
                'nama' => 'Jason Christopher Liendo',
                'tanggal_lahir' => '2003-08-01',
                'password' => 'Solusindo123',
                'kota' => 'KOTA SURABAYA',
                'kecamatan' => 'Sawahan',
                'nomor_telepon' => '0881026757313',
                'roles' => 'Owner',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
            [
                'id_account' => 'AC002',
                'username' => 'Lieming',
                'email' => 'closingsystem@gmail.com',
                'nama' => 'Markus Diyanto',
                'tanggal_lahir' => '1974-12-12',
                'password' => 'Solusindo123',
                'kota' => 'KOTA SURABAYA',
                'kecamatan' => 'Sawahan',
                'nomor_telepon' => '082211265859',
                'roles' => 'Pengosongan',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
            [
                'id_account' => 'AC003',
                'username' => 'jasonagent',
                'email' => 'solusindosinergi@gmail.com',
                'nama' => 'Jason Christopher Liendo',
                'tanggal_lahir' => '2003-08-01',
                'password' => 'Solusindo123',
                'kota' => 'KOTA SURABAYA',
                'kecamatan' => 'Sawahan',
                'nomor_telepon' => '0881026757313',
                'roles' => 'Agent',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
        ]);

        // Tabel Agent
        DB::table('agent')->insert([
            [
                'id_agent' => 'AG001',
                'id_account' => 'AC003',
                'nama' => 'Jason Christopher Liendo',
                'nomor_telepon' => '0881026757313',
                'email' => 'Jasoncliendo@gmail.com',
                'instagram' => 'Jasonchriss_',
                'facebook' => 'jasonchristopherliendo',
                'tanggal_join' => '2025-07-28',
                'picture' => '',
                'kota' => 'Surabaya',
                'status' => 'Aktif',
                'rating' => 0,
                'jumlah_penjualan' => 0,
                'lokasi_kerja' => 'Surabaya Barat',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
                'gambar_ktp' => '',
                'gambar_npwp' => ''
            ],

        ]);

    }
}
