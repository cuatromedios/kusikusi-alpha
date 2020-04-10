<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthtokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authtokens', function (Blueprint $table) {
            $table->char('token', 128)->primary();
            $table->uuid('user_id')->index();
            $table->char('created_ip', 45)->nullable();
            $table->char('updated_ip', 45)->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->timestampsTz();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authtokens');
    }
}
