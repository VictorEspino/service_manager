<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creador_id');
            $table->foreignId('de_id');
            $table->foreignId('topico_id');
            $table->string('asunto');
            $table->integer('prioridad');
            $table->integer('estatus')->default(1);
            $table->foreignId('asignado_a');
            $table->integer('actividad_actual')->default(0);
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
        Schema::dropIfExists('tickets');
    }
}
