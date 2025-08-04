<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkToPropertyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('property', function (Blueprint $table) {
            if (!Schema::hasColumn('property', 'link')) {
                $table->string('link', 500)->nullable()->unique()->after('id_agent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('property', function (Blueprint $table) {
            if (Schema::hasColumn('property', 'link')) {
                $table->dropUnique(['link']);
                $table->dropColumn('link');
            }
        });
    }
}
