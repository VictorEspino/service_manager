<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadTopicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_topicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topico_id');
            $table->integer('secuencia')->default(0);
            $table->string('descripcion')->default('PRINCIPAL');
            $table->integer('sla')->default(60);
            $table->foreignId('grupo_id');
            $table->foreignId('tipo_asignacion');
            $table->foreignId('user_id_automatico')->default(0);
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
        Schema::dropIfExists('actividad_topicos');
    }
}
