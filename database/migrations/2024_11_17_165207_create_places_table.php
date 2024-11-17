<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->double('coordinatex');
            $table->double('coordinatey');
            $table->string('photo')->nullable(); // URL фотографий
            $table->foreignId('base_id')->constrained('bases')->onDelete('cascade');
            $table->integer('tariffs_limit')->default(0);
            $table->boolean('is_del')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('places');
    }
}
