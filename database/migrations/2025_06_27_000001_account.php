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
        Schema::create('account', function (Blueprint $table) {
            $table->string('id_account', 50)->primary();
            $table->string('username', 50);
            $table->string('email', 100)->unique();
            $table->string('nama', 100);
            $table->date('tanggal_lahir');
            $table->string('password', 255);
            $table->string('kota', 100);
            $table->string('kecamatan', 100);
            $table->string('nomor_telepon', 15);

            $table->enum('roles', ['User', 'Agent', 'Pengosongan', 'Register', 'Owner', 'Pending'])->default('User');

            // Tanggal dibuat dan diupdate seperti MySQL CURRENT_TIMESTAMP behavior
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diupdate')->useCurrent()->useCurrentOnUpdate();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account');
    }
};
