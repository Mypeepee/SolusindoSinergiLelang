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
        Schema::create('referral_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('id_agent', 10); // ID agent yang share
            $table->unsignedBigInteger('id_listing'); // ID property yang diklik
            $table->string('ip', 50)->nullable(); // IP address pengunjung
            $table->string('user_agent', 255)->nullable(); // Browser/device info
            $table->timestamps();

            // Index untuk mempercepat query laporan
            $table->index(['id_agent', 'id_listing']);

            // Optional: Foreign key (kalau mau strict)
            // $table->foreign('id_agent')->references('id_agent')->on('agent')->onDelete('cascade');
            // $table->foreign('id_listing')->references('id_listing')->on('property')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_clicks');
    }
};
