<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->uuid();

            $table->date('date');

            $table->unsignedInteger('seats_count');
            $table->double('seat_price');
            $table->double('total');

            $table->double('discount')->default(0);

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trip_id');

            $table->string('email');


            $table->json('trip_details');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('trip_id')
                ->references('id')
                ->on('trips')
                ->onDelete('cascade');


            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
