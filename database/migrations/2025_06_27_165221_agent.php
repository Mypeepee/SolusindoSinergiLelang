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
        Schema::create('agent', function (Blueprint $table) {
            $table->string('id_agent', 10)->primary();
            $table->string('id_account', 50)->unique();
            $table->string('nama', 100)->nullable();
            $table->string('nomor_telepon', 15);
            $table->string('email', 100)->nullable();
            $table->string('instagram', 50)->nullable();
            $table->string('facebook', 50)->nullable();
            $table->date('tanggal_join')->nullable();
            $table->string('picture', 255)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('status', 20)->default('Aktif');
            $table->integer('rating')->nullable();
            $table->string('comment', 250)->nullable();
            $table->integer('jumlah_penjualan')->default(0);
            $table->string('lokasi_kerja', 100)->nullable();
            $table->string('gambar_ktp', 255)->nullable();
            $table->string('gambar_npwp', 255)->nullable();
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraint
            $table->foreign('id_account')->references('id_account')->on('account');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent');
    }
};
