<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_grup', function (Blueprint $table) {
            $table->bigIncrements('id_event');

            // penyelenggara event (opsional)
            $table->string('penyelenggara', 10)->nullable(); // FK -> agent.id_agent
            $table->foreign('penyelenggara')
                  ->references('id_agent')->on('agent')
                  ->nullOnDelete();

            $table->string('judul', 150);
            $table->dateTime('mulai');                 // waktu mulai ruang pemilu
            $table->dateTime('selesai')->nullable();   // opsional (bila ada batas akhir)

            // 'terbuka' = semua agent bisa join; 'tertutup' = hanya undangan
            $table->enum('akses_event', ['terbuka','tertutup'])->default('terbuka');

            // durasi giliran per peserta (menit) — default 5
            $table->unsignedInteger('durasi_giliran_menit')->default(5);

            $table->string('lokasi', 150)->nullable();

            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            $table->index(['mulai','selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_grup');
    }
};
