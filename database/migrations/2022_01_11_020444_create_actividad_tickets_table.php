<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id');
            $table->integer('secuencia')->default(0);
            $table->text('descripcion');
            $table->integer('sla')->default(60);
            $table->foreignId('grupo_id');
            $table->foreignId('tipo_asignacion');
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
        Schema::dropIfExists('actividad_tickets');
    }
}
