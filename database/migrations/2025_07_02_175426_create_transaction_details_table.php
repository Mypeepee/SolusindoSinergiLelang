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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT primary key

            $table->string('id_account', 50);
            $table->string('id_transaction', 10);

            $table->enum('status_transaksi', [
                'Closing',
                'Kutipan Risalah Lelang',
                'Akte Grosse',
                'Balik Nama',
                'Eksekusi Pengosongan',
                'Selesai'
            ])->default('Closing');
            $table->text('catatan')->nullable();

            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('id_account')->references('id_account')->on('account')->onDelete('cascade');
            $table->foreign('id_transaction')->references('id_transaction')->on('transaction')->onDelete('cascade');

            // Indexes
            $table->index('id_account');
            $table->index('id_transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
