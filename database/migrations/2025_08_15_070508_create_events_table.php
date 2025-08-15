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
        Schema::create('events', function (Blueprint $table) {
            $table->id('id_event');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->dateTime('mulai');
            $table->dateTime('selesai')->nullable();
            $table->boolean('all_day')->default(false);
            $table->enum('akses', ['Terbuka','Tertutup'])->default('Terbuka');
            $table->string('location', 255)->nullable();
            $table->string('created_by');
            $table->unsignedInteger('durasi')->default(5);
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('created_by');

            // Foreign keys
            $table->foreign('created_by')
                ->references('id_account')->on('account')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
