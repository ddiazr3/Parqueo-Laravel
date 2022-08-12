<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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

            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('user_creacion_id');
            $table->unsignedBigInteger('user_salida_id');
            $table->unsignedBigInteger('tipo_placa_id');
            $table->string('placa');
            $table->string("descripcion")->nullable();
            $table->timestamp('fecha_ingreso')->nullable();
            $table->timestamp('fecha_egreso')->nullable();
            $table->foreign('tipo_placa_id')->references('id')->on('tipo_placas')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_creacion_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_salida_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('cascade');
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
};
