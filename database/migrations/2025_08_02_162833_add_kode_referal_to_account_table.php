<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->string('kode_referal', 10)->nullable()->after('roles');
        });
    }

    public function down()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropColumn('kode_referal');
        });
    }
};
