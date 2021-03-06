<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketAvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_avances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id');
            $table->foreignId('user_id');
            $table->string('nombre_usuario');
            $table->text('avance');
            $table->integer('tipo_avance');
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
        Schema::dropIfExists('ticket_avances');
    }
}
