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
            $table->enum('status', ['invited', 'accepted', 'declined'])
                ->default('invited');
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('id_event');
            $table->index('id_account');

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
