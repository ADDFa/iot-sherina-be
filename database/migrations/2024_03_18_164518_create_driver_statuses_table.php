<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("driver_statuses", function (Blueprint $table) {
            $table->id();
            $table->foreignId("driver_id")->constrained("drivers");
            $table->integer("blink_count");
            $table->float("confidence");
            $table->enum("eye_status", ["Terbuka", "Tertutup"]);
            $table->enum("state_status", ["Lelah", "Normal"]);
            $table->bigInteger("session");
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
        Schema::dropIfExists("driver_statuses");
    }
};
