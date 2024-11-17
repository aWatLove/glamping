<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasesTable extends Migration
{
    public function up()
    {
        Schema::create('bases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->double('coordinate_x');
            $table->double('coordinate_y');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bases');
    }
}
