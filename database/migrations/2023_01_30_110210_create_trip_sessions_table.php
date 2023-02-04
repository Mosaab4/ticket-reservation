<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('trip_sessions', function (Blueprint $table) {
            $table->id();

            $table->uuid();

            $table->json('seats');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trip_id');

            $table->date('date');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_sessions');
    }
};
