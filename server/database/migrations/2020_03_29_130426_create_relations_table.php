<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

class CreateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relations', function (Blueprint $table) {
            $table->bigIncrements('relation_id');
            $table->bigInteger('caller_entity_id')->index('caller')->unsigned();
            $table->bigInteger('called_entity_id')->index('called')->unsigned();
            $table->string('kind', 25)->default('relation')->index('kind');
            $table->integer('position')->unsigned()->default(0);
            $table->integer('depth')->unsigned()->default(0);
            $table->json('tags')->nullable();
            $table->timestampsTz();
            $table->foreign('caller_entity_id')->references('id')->on('entities');
            $table->foreign('called_entity_id')->references('id')->on('entities');
            $table->index(['caller_entity_id', 'called_entity_id', 'kind'], 'relation_search');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relations');
    }
}
