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
        Schema::create('listingan_agent', function (Blueprint $table) {
            $table->id(); // Kolom 'id' AUTO_INCREMENT, primary key
            $table->string('agent_id', 20)->nullable();
            $table->unsignedBigInteger('property_id')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('agent_id')->references('id_account')->on('agent')->onDelete('set null');
            $table->foreign('property_id')->references('id_listing')->on('property')->onDelete('set null');
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
