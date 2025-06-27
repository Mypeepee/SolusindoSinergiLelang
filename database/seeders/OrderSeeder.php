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
                'username' => 'jevon123',
                'email' => 'jevon@gmail.com',
                'nama' => 'Jevon',
                'tanggal_lahir' => '1995-04-10',
                'password' => Hash::make('jevon123'),
                'kota' => 'Surabaya',
                'kecamatan' => 'Tegalsari',
                'nomor_telepon' => '081234567890',
                'roles' => 'Agent',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
            [
                'id_account' => 'AC002',
                'username' => 'felicia88',
                'email' => 'felicia@gmail.com',
                'nama' => 'Felicia',
                'tanggal_lahir' => '1992-07-21',
                'password' => Hash::make('feli123'),
                'kota' => 'Bandung',
                'kecamatan' => 'Cicendo',
                'nomor_telepon' => '082233445566',
                'roles' => 'Agent',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
            [
                'id_account' => 'AC003',
                'username' => 'valenx',
                'email' => 'valen@gmail.com',
                'nama' => 'Valen',
                'tanggal_lahir' => '1990-12-05',
                'password' => Hash::make('valen123'),
                'kota' => 'Jakarta',
                'kecamatan' => 'Kemayoran',
                'nomor_telepon' => '083344556677',
                'roles' => 'Agent',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
        ]);

        // Tabel Agent
        DB::table('agent')->insert([
            [
                'id_agent' => 'AG001',
                'id_account' => 'AC001',
                'nama' => 'Jevon',
                'nomor_telepon' => '081234567890',
                'email' => 'jevon@gmail.com',
                'instagram' => 'jevon.ig',
                'facebook' => 'fb.com/jevon',
                'jadwal' => '2025-06-01',
                'picture' => '/storage/agents/jevon.jpg',
                'kota' => 'Surabaya',
                'status' => 'Aktif',
                'rating' => 4,
                'jumlah_penjualan' => 2,
                'lokasi_kerja' => 'Surabaya Barat',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
                'gambar_ktp' => '/storage/ktp/jevon_ktp.jpg',
                'gambar_npwp' => '/storage/npwp/jevon_npwp.jpg'
            ],
            [
                'id_agent' => 'AG002',
                'id_account' => 'AC002',
                'nama' => 'Felicia',
                'nomor_telepon' => '082233445566',
                'email' => 'felicia@gmail.com',
                'instagram' => 'felicia.ig',
                'facebook' => 'fb.com/felicia',
                'jadwal' => '2025-06-02',
                'picture' => '/storage/agents/feli.jpg',
                'kota' => 'Bandung',
                'status' => 'Aktif',
                'rating' => 5,
                'jumlah_penjualan' => 1,
                'lokasi_kerja' => 'Bandung Utara',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
                'gambar_ktp' => '/storage/ktp/feli_ktp.jpg',
                'gambar_npwp' => '/storage/npwp/feli_npwp.jpg'
            ],
            [
                'id_agent' => 'AG003',
                'id_account' => 'AC003',
                'nama' => 'Valen',
                'nomor_telepon' => '083344556677',
                'email' => 'valen@gmail.com',
                'instagram' => 'valen.ig',
                'facebook' => 'fb.com/valen',
                'jadwal' => '2025-06-03',
                'picture' => '/storage/agents/valen.jpg',
                'kota' => 'Jakarta',
                'status' => 'Aktif',
                'rating' => 3,
                'jumlah_penjualan' => 0,
                'lokasi_kerja' => 'Jakarta Selatan',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
                'gambar_ktp' => '/storage/ktp/valen_ktp.jpg',
                'gambar_npwp' => '/storage/npwp/valen_npwp.jpg'
            ],
        ]);

        DB::table('property')->insert([
            [
                'id_listing' => 1,
                'judul' => 'Rumah Mewah di Jakarta',
                'deskripsi' => 'Rumah mewah dengan fasilitas lengkap di pusat kota.',
                'tipe' => 'rumah',
                'kamar_tidur' => 4,
                'kamar_mandi' => 3,
                'harga' => 2500000000,
                'lokasi' => 'Jl. Merdeka No. 10, Jakarta',
                'lantai' => 2,
                'id_agent' => 'AGT001',
                'luas_tanah' => 300,
                'luas_bangunan' => 250,
                'provinsi' => 'DKI Jakarta',
                'kota' => 'Jakarta Pusat',
                'kelurahan' => 'Gambir',
                'sertifikat' => 'SHM',
                'orientation' => 'selatan',
                'status' => 'Tersedia',
                'gambar' => '/storage/property-images/1748617589_6839c97557ddf.jpg',
                'payment' => 'cash,kpr',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
            [
                'id_listing' => 2,
                'judul' => 'Apartemen Modern Surabaya',
                'deskripsi' => 'Apartemen dengan view kota Surabaya dan fasilitas premium.',
                'tipe' => 'apartemen',
                'kamar_tidur' => 2,
                'kamar_mandi' => 1,
                'harga' => 750000000,
                'lokasi' => 'Jl. Basuki Rahmat, Surabaya',
                'lantai' => 15,
                'id_agent' => 'AGT002',
                'luas_tanah' => 80,
                'luas_bangunan' => 100,
                'provinsi' => 'Jawa Timur',
                'kota' => 'Surabaya',
                'kelurahan' => 'Tegalsari',
                'sertifikat' => 'HGB',
                'orientation' => 'utara',
                'status' => 'Tersedia',
                'gambar' => '/storage/property-images/1748617589_6839c97557ddf.jpg',
                'payment' => 'cash',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
            [
                'id_listing' => 3,
                'judul' => 'Ruko Strategis Bandung',
                'deskripsi' => 'Ruko 2 lantai dekat pusat perbelanjaan.',
                'tipe' => 'ruko',
                'kamar_tidur' => 1,
                'kamar_mandi' => 2,
                'harga' => 1250000000,
                'lokasi' => 'Jl. Riau No. 5, Bandung',
                'lantai' => 2,
                'id_agent' => 'AGT003',
                'luas_tanah' => 150,
                'luas_bangunan' => 200,
                'provinsi' => 'Jawa Barat',
                'kota' => 'Bandung',
                'kelurahan' => 'Cihapit',
                'sertifikat' => 'AJB',
                'orientation' => 'barat',
                'status' => 'Tersedia',
                'gambar' => '/storage/property-images/1748617589_6839c97557ddf.jpg',
                'payment' => 'cash,kpr',
                'tanggal_dibuat' => now(),
                'tanggal_diupdate' => now(),
            ],
        ]);

    }
}
