<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadCamposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_campos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id');
            $table->string('etiqueta');
            $table->string('tipo_control');
            $table->boolean('requerido');
            $table->foreignId('lista_id');
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
        Schema::dropIfExists('actividad_campos');
    }
}
