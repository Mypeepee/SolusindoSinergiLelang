<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemilu_pilihan', function (Blueprint $table) {
            $table->id('id');

            $table->unsignedBigInteger('id_event');   // FK -> event_grup.id_event
            $table->string('id_agent', 10);           // FK -> agent.id_agent
            $table->unsignedBigInteger('id_listing'); // FK -> property.id  (ganti jika nama tabelmu berbeda)

            $table->foreign('id_event')->references('id_event')->on('events')->cascadeOnDelete();
            $table->foreign('id_agent')->references('id_agent')->on('agent')->cascadeOnDelete();
            // Ubah 'property' sesuai tabel listing kamu:
            // $table->foreign('id_listing')->references('id')->on('property')->cascadeOnDelete();

            // 1 listing hanya boleh diambil 1x per event
            $table->unique(['id_event','id_listing']);
            // cegah duplikasi yang sama oleh agent yang sama (opsional, bagus untuk data bersih)
            $table->unique(['id_event','id_agent','id_listing']);

            $table->index(['id_event','id_agent']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemilu_pilihan');
    }
};
