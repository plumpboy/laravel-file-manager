<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->string('id', 100)->primary();

            $table->string('name', 255);
            $table->string('type', 20);
            $table->string('bucket', 255);
            $table->string('disk', 50);
            $table->string('extension', 20);
            $table->unsignedInteger('dir_id');
            $table->string('visible', 10);
            //
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
        Schema::dropIfExists('files');
    }
}
