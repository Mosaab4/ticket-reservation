<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->uuid();

            $table->unsignedBigInteger('from_id');
            $table->unsignedBigInteger('to_id');

            $table->double('price');

            $table->unsignedInteger('distance');

            $table->unsignedBigInteger('bus_id');

            $table->foreign('bus_id')
                ->references('id')
                ->on('buses')
                ->onDelete('cascade');

            $table->foreign('from_id')
                ->references('id')
                ->on('stations')
                ->onDelete('cascade');

            $table->foreign('to_id')
                ->references('id')
                ->on('stations')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trips');
    }
};
