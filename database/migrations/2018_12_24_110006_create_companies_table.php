<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sort_id')->nullable();
            $table->integer('region_id')->references('id')->on('regions');
            $table->string('slug');
            $table->string('title');
            $table->string('image')->nullable();
            $table->text('about')->nullable();
            $table->string('phones')->nullable();
            $table->string('links')->nullable();
            $table->string('emails')->nullable();
            $table->string('address')->nullable();
            $table->string('schedule')->nullable();
            $table->char('lang', 4);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
