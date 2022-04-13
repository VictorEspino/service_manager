<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargaEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carga_empleados', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_empleado');
            $table->string('nombre');
            $table->string('area');
            $table->string('subarea');
            $table->string('puesto');
            $table->string('estatus');
            $table->string('carga_id');
            $table->foreignId('user_id_carga');
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
        Schema::dropIfExists('carga_empleados');
    }
}
