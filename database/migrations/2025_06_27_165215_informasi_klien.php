<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('informasi_klien', function (Blueprint $table) {
            $table->string('id_account', 50);
            $table->string('berlaku_hingga', 50)->nullable();
            $table->string('gambar_ktp', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nik', 20);
            $table->string('pekerjaan', 100)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('status_verifikasi', ['Pending', 'Terverifikasi', 'Ditolak'])->default('Pending');
            $table->string('nomor_rekening', 30)->nullable();
            $table->string('nama_bank', 50)->nullable();
            $table->string('atas_nama', 100)->nullable();
            $table->string('nomor_npwp', 30)->nullable();
            $table->string('gambar_npwp', 255)->nullable();
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Foreign key
            $table->foreign('id_account')->references('id_account')->on('account');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi_klien');
    }
};
