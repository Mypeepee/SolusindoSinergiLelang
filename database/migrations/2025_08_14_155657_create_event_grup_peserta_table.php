<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_grup_peserta', function (Blueprint $table) {
            $table->unsignedBigInteger('id_event');   // FK -> event_grup.id_event
            $table->string('id_agent', 10);           // FK -> agent.id_agent

            $table->primary(['id_event','id_agent']);

            // keikutsertaan
            $table->enum('status_rsvp', ['belum','hadir','tidak_hadir'])->default('belum');

            // ===== GABUNG GILIRAN =====
            $table->unsignedInteger('urutan')->nullable();      // 1,2,3,... (null = belum dijadwalkan)
            $table->dateTime('mulai_giliran')->nullable();      // bisa diisi auto dari event.mulai
            $table->dateTime('selesai_giliran')->nullable();    // = mulai_giliran + durasi
            $table->enum('status_giliran', ['menunggu','berjalan','selesai'])->default('menunggu');

            $table->timestamp('tanggal_dibuat')->useCurrent();

            $table->foreign('id_event')->references('id_event')->on('event_grup')->cascadeOnDelete();
            $table->foreign('id_agent')->references('id_agent')->on('agent')->cascadeOnDelete();

            // urutan harus unik per event (NOTE: NULL boleh dobel — wajar untuk yg blm dijadwalkan)
            $table->unique(['id_event','urutan']);

            $table->index(['id_event','mulai_giliran','selesai_giliran']);
            $table->index(['id_agent','id_event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_grup_peserta');
    }
};
