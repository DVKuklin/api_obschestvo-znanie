<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeadingImageToThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->mediumText('heading_image')->nullable()->after('emoji');
            $table->mediumText('heading_mobile_image')->nullable()->after('heading_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('heading_image');
            $table->dropColumn('heading_mobile_image');
        });
    }
}
