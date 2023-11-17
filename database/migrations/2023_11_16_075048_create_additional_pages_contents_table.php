<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalPagesContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_pages_contents', function (Blueprint $table) {
            $table->id();
            $table->integer('sort');
            $table->mediumText('content')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedBigInteger('additional_page_id')->nullable();
            $table->timestamps();

            $table->foreign('additional_page_id')
                    ->references('id')
                    ->on('additional_pages')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_pages_contents');
    }
}
