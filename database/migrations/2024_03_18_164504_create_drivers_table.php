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
        Schema::create("drivers", function (Blueprint $table) {
            $table->id();
            $table->foreignId("credential_id")->unique()->constrained("credentials")->cascadeOnDelete();
            $table->string("name");
            $table->bigInteger("session")->default(1);
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
        Schema::dropIfExists("drivers");
    }
};
