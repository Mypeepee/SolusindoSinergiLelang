<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property', function (Blueprint $table) {
            $table->id('id_listing'); // AUTO_INCREMENT, PRIMARY KEY
            $table->string('id_agent', 50)->nullable(); // relasi ke account.id_account
            $table->string('judul')->nullable();
            $table->string('deskripsi', 2200)->nullable();
            $table->string('tipe', 15)->nullable();
            $table->bigInteger('harga')->nullable();
            $table->string('lokasi', 500)->nullable();
            $table->integer('luas')->nullable();
            $table->string('provinsi', 255)->nullable();
            $table->string('kota', 70)->nullable();
            $table->string('kelurahan', 70)->nullable();
            $table->string('sertifikat', 70)->nullable();
            $table->enum('status', ['Tersedia', 'Terjual'])->nullable(); // check constraint
            $table->string('gambar', 900)->nullable();
            $table->string('payment', 20)->nullable();

            $table->bigInteger('uang_jaminan')->nullable();
            $table->date('batas_akhir_jaminan')->nullable();
            $table->date('batas_akhir_penawaran')->nullable();

            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Optional: Add foreign key if you want to link with `account` table
            $table->foreign('id_agent')->references('id_agent')->on('agent')->onDelete('set null');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('property');
    }
};
