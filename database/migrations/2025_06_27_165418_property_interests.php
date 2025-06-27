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
        Schema::create('property_interests', function (Blueprint $table) {
            $table->unsignedBigInteger('id_listing');
            $table->string('id_klien', 50);
            $table->string('ktp', 255);
            $table->string('npwp', 255);
            $table->string('buku_tabungan', 255);
            $table->enum('status', [
                'followup',
                'pending',
                'gagal',
                'closing',
                'buyer_meeting',
                'kutipan_risalah_lelang',
                'akte_grosse',
                'balik_nama'
            ])->default('pending');
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('id_listing');
            $table->index('id_klien');

            // Foreign keys
            $table->foreign('id_listing')
                ->references('id_listing')->on('property')
                ->onDelete('cascade');

            $table->foreign('id_klien')
                ->references('id_account')->on('account')
                ->onDelete('cascade');
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
