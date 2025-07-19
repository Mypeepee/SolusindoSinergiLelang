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
        Schema::create('transaction', function (Blueprint $table) {
            $table->string('id_transaction', 10)->primary();

            $table->string('id_agent', 10);
            $table->string('id_klien', 50);
            $table->unsignedBigInteger('id_listing');

            $table->bigInteger('harga_deal');
            $table->bigInteger('harga_bidding');
            $table->bigInteger('selisih');
            $table->bigInteger('komisi_agent');

            $table->enum('status_transaksi', [
                'Closing',
                'Kutipan Risalah Lelang',
                'Akte Grosse',
                'Balik Nama',
                'Eksekusi Pengosongan',
                'Selesai'
            ])->default('Closing');

            $table->date('tanggal_transaksi');
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            $table->integer('rating')->nullable();
            $table->string('comment', 250)->nullable();

            // Foreign keys
            $table->foreign('id_agent')->references('id_agent')->on('agent')->onDelete('cascade');
            $table->foreign('id_klien')->references('id_account')->on('account')->onDelete('cascade');
            $table->foreign('id_listing')->references('id_listing')->on('property')->onDelete('cascade');

            // Indexes (optional since Laravel auto-generates for foreign keys)
            $table->index('id_agent');
            $table->index('id_klien');
            $table->index('id_listing');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');

    }
};
