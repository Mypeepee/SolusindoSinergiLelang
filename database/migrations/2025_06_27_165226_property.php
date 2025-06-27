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
        Schema::create('property', function (Blueprint $table) {
            $table->id('id_listing'); // AUTO_INCREMENT, PRIMARY KEY
            $table->string('judul')->nullable();
            $table->string('deskripsi', 2200)->nullable();
            $table->string('tipe', 15)->nullable();
            $table->integer('kamar_tidur')->nullable();
            $table->integer('kamar_mandi')->nullable();
            $table->bigInteger('harga')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->integer('lantai'); // NOT NULL
            $table->string('id_agent', 50)->nullable(); // relasi ke account.id_account
            $table->integer('luas_tanah')->nullable();
            $table->integer('luas_bangunan')->nullable();
            $table->string('provinsi', 255)->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('kelurahan', 50)->nullable();
            $table->string('sertifikat', 50)->nullable();
            $table->string('orientation', 15)->nullable();
            $table->enum('status', ['Tersedia', 'Terjual'])->nullable(); // check constraint
            $table->string('gambar', 500)->nullable();
            $table->string('payment', 20)->nullable();
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Optional: Add foreign key if you want to link with `account` table
            // $table->foreign('id_agent')->references('id_account')->on('account')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
