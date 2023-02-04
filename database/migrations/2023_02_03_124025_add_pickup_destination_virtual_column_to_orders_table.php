<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        if (App::environment() == 'testing') {
            DB::statement("
                ALTER TABLE orders
                ADD pickup_destination varchar(255)
                GENERATED ALWAYS AS
                (
                    LOWER(JSON_EXTRACT(trip_details,'$.from')) ||
                    '-' ||
                    LOWER(JSON_EXTRACT(trip_details,'$.to'))
                )
                VIRTUAL NULL;
            ");
        } else {
            DB::statement("
                ALTER TABLE orders
                ADD pickup_destination varchar(255)
                GENERATED ALWAYS AS
                (
                    CONCAT(
                        LOWER(JSON_UNQUOTE(JSON_EXTRACT(trip_details,'$.from'))),
                        '-',
                        LOWER(JSON_UNQUOTE(JSON_EXTRACT(trip_details,'$.to')))
                    )
                )
                VIRTUAL NULL;
            ");
        }
    }

    public function down()
    {
        DB::statement("ALTER TABLE orders DROP COLUMN pickup_destination;");
    }
};
