<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DepartamentoEmpresaTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('rel_departamento_empresa', function ($table) {
            $table->increments('id');
            $table->integer('empresa_id')->unsigned()->nullable();
            $table->foreign('empresa_id')->references('id')->on('empresa');
            $table->integer('departamento_id')->unsigned()->nullable();
            $table->foreign('departamento_id')->references('id')->on('departamento');
            $table->integer('usuario_id')->unsigned()->nullable();
            $table->foreign('usuario_id')->references('id')->on('usuario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

    public function deleteAllTables() {
        Schema::dropIfExists('propiedad');
    }
}
