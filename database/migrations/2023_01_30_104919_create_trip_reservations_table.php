<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('trip_reservations', function (Blueprint $table) {
            $table->id();

            $table->date('date');

            $table->boolean('locked')->default(0);
            $table->unsignedBigInteger('lock_user_id')->nullable();
            $table->dateTime('locked_at')->nullable();

            $table->unsignedBigInteger('remaining_seats');

            $table->unsignedBigInteger('trip_id');

            $table->foreign('trip_id')
                ->references('id')
                ->on('trips')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_reservations');
    }
};
