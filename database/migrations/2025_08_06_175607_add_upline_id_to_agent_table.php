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
        Schema::table('agent', function (Blueprint $table) {
            $table->string('upline_id', 10)->nullable()->after('id_account');
            $table->foreign('upline_id')->references('id_agent')->on('agent')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('agent', function (Blueprint $table) {
            $table->dropForeign(['upline_id']);
            $table->dropColumn('upline_id');
        });
    }
};
