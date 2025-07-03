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
            $table->id();
            $table->unsignedBigInteger('id_listing');
            $table->string('id_klien', 50);
            $table->enum('status', [
                'Pending',
                'FollowUp',
                'BuyerMeeting',
                'Gagal',
                'Closing'
            ])->default('Pending');
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
        Schema::dropIfExists('property_interests');
    }
};
