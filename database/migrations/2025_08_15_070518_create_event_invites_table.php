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
        Schema::create('event_invites', function (Blueprint $table) {
            $table->id('id_invite');
            $table->unsignedBigInteger('id_event');
            $table->string('id_account');
            $table->enum('status', ['Diundang', 'Hadir', 'Tidak Hadir'])
                ->default('Diundang');

            // ===== GABUNG GILIRAN =====
            $table->unsignedInteger('urutan')->nullable();      // 1,2,3,... (null = belum dijadwalkan)
            $table->dateTime('mulai_giliran')->nullable();      // bisa diisi auto dari event.mulai
            $table->dateTime('selesai_giliran')->nullable();    // = mulai_giliran + durasi
            $table->enum('status_giliran', ['Menunggu','Berjalan','Selesai'])->default('Menunggu');

            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('id_event');
            $table->index('id_account');
            $table->unique(['id_event','urutan']);

            // Foreign keys
            $table->foreign('id_event')
                ->references('id_event')->on('events')
                ->onDelete('cascade');

            $table->foreign('id_account')
                ->references('id_account')->on('account')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_invites');
    }
};
