<?php

namespace App\Models;

use App\Helpers\JwtHelper;
use App\Helpers\ModelsHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class Usuario extends Model {
    const STATUS_ACTIVO = 'ACT';
    const STATUS_NUEVO = 'NVO';
    const STATUS_INACTIVO = 'INA';

    public $table = 'usuario';
    public $fillable = ['nombre','fecha_nacimiento', 'email', 'genero','telefono','celular', 'fecha_ingreso','rol_id', 'password', 'ultimo_login'];

//|------Attributes------|//
//|------Relaciones------|//
    public function rol() { return $this->belongsTo(Rol::class, 'rol_id'); }

    public function foto() { return $this->belongsTo(Rol::class, 'foto_id'); }

//|------Scopes------|//
    public function scopePorEmail($q, $email) { return $q->where('email', $email); }

    public function scopePorId($q, $id) { return $q->where('id', $id); }

//|------Funciones de la clase------|//
    public function eliminar() {
        $auts = Autorizacion::where('usuario_id', $this->id)->get();
        foreach ($auts ?? [] as $aut)
            $aut->actualizar(['usuario_id' => null]);
        $this->delete();
    }

    public function actualizar($arr_info) {
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, []);
        $this->fill($info_guardar);
        $this->save();
        return $this;
    }

    public function asignarRol($rol_id) { $this->actualizar(compact('rol_id')); }

    public function can($permiso) {
        foreach ($this->rol->rels_rol_permiso ?? [] as $rel) {
            $nombre = $rel->permiso->seccion_permiso->nombre . "." . $rel->permiso->nombre;
            return $permiso == $nombre;
        };
        return false;
    }

    public function quitarRol() { $this->actualizar(['rol_id' => null]); }

    public function activar() { $this->actualizar(['fecha_verificacion' => Carbon::now()->toDateTimeString(), 'estatus' => self::STATUS_ACTIVO]); }

//|------Funciones Estáticas------|//
    public static function guardar($arr_info) {
        $reg = isset($arr_info['id']) ? self::find($arr_info['id']) : new self();
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['rol_id', 'info_personal_id']);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }

    public static function findActual() {
        $helper = new JwtHelper();
        Log::debug(request()->get('token'));
        $usuario_id = $helper->getUsuarioIdPlain(request()->get('token'));
        return self::find($usuario_id);
    }

    public static function generaPassword() {
        $palabras = ['Alfa', 'Bravo', 'Delta', 'Golf', 'India', 'Kilo', 'Sierra', 'Tango'];
        $palabra = $palabras[rand(0, 7)];
        $numero = rand(10, 99);
        return $palabra . $numero;
    }
}
