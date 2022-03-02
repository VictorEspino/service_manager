<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoComunicacionPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_comunicacion_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignID('grupo_id');
            $table->foreignId('user_id');
            $table->string('nombre_usuario');
            $table->text('post');
            $table->boolean('adjunto')->default(0);
            $table->string('archivo_adjunto')->nullable();
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
        Schema::dropIfExists('grupo_comunicacion_posts');
    }
}
