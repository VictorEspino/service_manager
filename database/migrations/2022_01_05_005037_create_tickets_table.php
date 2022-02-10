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
            $table->integer('emite_autorizacion')->default(0);
            $table->integer('resultado_autorizacion')->default(0);
            $table->string('asunto');
            $table->integer('prioridad');
            $table->integer('privacidad')->default(1); //privado
            $table->integer('n_actividades');
            $table->integer('n_minutos');
            $table->integer('estatus')->default(1);
            $table->foreignId('asignado_a');
            $table->integer('actividad_actual')->default(0);
            $table->boolean('adjunto')->default(0);
            $table->string('archivo_adjunto')->nullable();
            $table->foreignId('user_cerrador')->nullable();
            $table->string('nombre_cerrador')->nullable();
            $table->timestamp('cierre_at')->nullable();
            $table->integer('time_to')->default(0);
            $table->integer('t_solicitante')->default(0);
            $table->integer('t_a0')->default(0);
            $table->integer('t_a1')->default(0);
            $table->integer('t_a2')->default(0);
            $table->integer('t_a3')->default(0);
            $table->integer('t_a4')->default(0);
            $table->integer('t_a5')->default(0);
            $table->integer('t_a6')->default(0);
            $table->integer('t_a7')->default(0);
            $table->integer('t_a8')->default(0);
            $table->integer('t_a9')->default(0);
            $table->integer('a_a0')->default(0);
            $table->integer('a_a1')->default(0);
            $table->integer('a_a2')->default(0);
            $table->integer('a_a3')->default(0);
            $table->integer('a_a4')->default(0);
            $table->integer('a_a5')->default(0);
            $table->integer('a_a6')->default(0);
            $table->integer('a_a7')->default(0);
            $table->integer('a_a8')->default(0);
            $table->integer('a_a9')->default(0);
            
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
