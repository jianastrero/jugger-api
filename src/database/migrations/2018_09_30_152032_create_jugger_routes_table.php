<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJuggerRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jugger_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_name');
            $table->string('slug');
            $table->text('columns');
            $table->text('sort');
            $table->boolean('column_override')->default(false);
            $table->boolean('sort_override')->default(false);
            $table->boolean('create')->default(true);
            $table->boolean('read')->default(true);
            $table->boolean('update')->default(true);
            $table->boolean('delete')->default(true);
            $table->boolean('list')->default(true);
            $table->unsignedInteger('paginate_item_count')->default(50);
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
        Schema::dropIfExists('jugger_routes');
    }
}
