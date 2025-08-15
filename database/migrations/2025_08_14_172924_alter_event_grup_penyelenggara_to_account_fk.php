<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_grup', function (Blueprint $table) {
            // Hapus FK lama ke agent
            $table->dropForeign('event_grup_penyelenggara_foreign');

            // Sesuaikan panjang kolom jika id_account > 10 (misal 50)
            $table->string('penyelenggara', 50)->nullable()->change();

            // Tambah FK baru ke account.id_account
            $table->foreign('penyelenggara')
                  ->references('id_account')
                  ->on('account')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_grup', function (Blueprint $table) {
            $table->dropForeign('event_grup_penyelenggara_foreign');
            $table->string('penyelenggara', 10)->nullable()->change();
            $table->foreign('penyelenggara')
                  ->references('id_agent')
                  ->on('agent')
                  ->nullOnDelete();
        });
    }
};
