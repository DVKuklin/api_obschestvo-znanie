<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCollationInDescriptionInThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    //При попытке выполнить миграции на сервере, данная миграция выкинула исключение
    //Смысл миграции в том, что бы сделать кодировку поля description таблицы themes такой же как кодировка у поля content таблицы paragraphs
    //В итоге сделал вручную, потому что на сервере такой кодировки как на локале не было, и у content почему то другая оказалась.

    public function up()
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->longText('description')->nullable()->default(null)->collation('utf8mb4_0900_ai_ci')->change();
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
            $table->longText('description')->nullable()->default(null)->collation('utf8mb4_unicode_ci')->change();
        });
    }
}
