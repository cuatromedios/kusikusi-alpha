<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model');
            $table->json('content')->nullable();
            $table->string('view')->nullable();
            $table->uuid('parent_entity_id')->index('parent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->dateTimeTz('published_at')->default('2000-01-01 00:00:00');
            $table->dateTimeTz('unpublished_at')->default('9999-12-31 23:59:59');
            $table->integer('version')->unsigned()->default(1);
            $table->integer('version_tree')->unsigned()->default(1);
            $table->integer('version_relations')->unsigned()->default(1);
            $table->integer('version_full')->unsigned()->default(1);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities');
    }
}
