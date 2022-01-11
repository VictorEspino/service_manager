<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadTicketCamposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_ticket_campos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_ticket_id');
            $table->string('etiqueta');
            $table->string('tipo_control');
            $table->boolean('requerido');
            $table->foreignId('lista_id');
            $table->integer('referencia');
            $table->string('valor')->nullable();
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
        Schema::dropIfExists('actividad_ticket_campos');
    }
}
