<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tablasbase extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $this->deleteAllTables();
        Schema::create('rol', function ($table) {
            $table->increments('id');
            $table->string('nombre', 50);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::create('seccion_permiso', function ($table) {
            $table->increments('id');
            $table->string('nombre', 50);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::create('permiso', function ($table) {
            $table->increments('id');
            $table->string('nombre', 80);
            $table->string('descripcion', 100);
            $table->integer('seccion_id')->unsigned();
            $table->foreign('seccion_id')->references('id')->on('seccion_permiso');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::create('rel_rol_permiso', function ($table) {
            $table->increments('id');
            $table->integer('rol_id')->unsigned();
            $table->foreign('rol_id')->references('id')->on('rol');
            $table->integer('permiso_id')->unsigned();
            $table->foreign('permiso_id')->references('id')->on('permiso');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::create('token_blacklist', function ($table) {
            $table->increments('id');
            $table->string('token', 25);
            $table->timestamps();
        });
        Schema::create('tipo_configuracion', function ($table) {
            $table->increments('id');
            $table->string('nombre', 50);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::create('configuracion', function ($table) {
            $table->increments('id');
            $table->string('nombre', 50);
            $table->string('descripcion', 250);
            $table->integer('tipo_configuracion_id')->unsigned();
            $table->foreign('tipo_configuracion_id')->references('id')->on('tipo_configuracion');
            $table->string('valor', 200);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 250);
            $table->date('fecha_nacimiento');
            $table->string('email')->unique();
            $table->string('genero',1);
            $table->string('telefono',13);
            $table->string('celular',13);
            $table->date('fecha_ingreso');
            $table->integer('rol_id')->unsigned()->nullable();
            $table->foreign('rol_id')->references('id')->on('rol');
            $table->string('password');
            $table->timestamps();
        });
        Schema::create('autorizacion', function ($table) {
            $table->increments('id');
            $table->dateTime('fecha');
            $table->integer('usuario_id')->unsigned();
            $table->foreign('usuario_id')->references('id')->on('usuario');
            $table->string('estatus', 1)->nullable();
            $table->string('comentario', 150)->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $this->deleteAllTables();
    }

    public function deleteAllTables() {
        Schema::dropIfExists('autorizacion');
        Schema::dropIfExists('empleado');
        Schema::dropIfExists('configuracion');
        Schema::dropIfExists('tipo_configuracion');
        Schema::dropIfExists('token_blacklist');
        Schema::dropIfExists('rel_rol_permiso');
        Schema::dropIfExists('permiso');
        Schema::dropIfExists('seccion_permiso');
        Schema::dropIfExists('rol');
    }
}
