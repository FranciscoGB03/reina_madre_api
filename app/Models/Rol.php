<?php

namespace App\Models;

use App\Helpers\ModelsHelper;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class Rol extends Model {

    public $table = 'rol';
    public $fillable = ['nombre'];

//|------Attributes------|//
//|------Relaciones------|//
    public function rels_rol_permiso() { return $this->hasMany(RelRolPermiso::class, 'rol_id'); }

    public function usuarios() { return $this->hasMany(Usuario::class, 'rol_id'); }

//|------Scopes------|//
    public function scopeActivo($q) { return $q; }

//|------Funciones de la clase------|//
    public function eliminar() { $this->delete(); }

    public function actualizar($arr_info) {
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, []);
        $this->fill($info_guardar);
        $this->save();
        return $this;
    }

    public function guardarPermisos($rels_rol_permiso) {
        //Eliminando permisos no usados
        ModelsHelper::eliminarNoVigentes($rels_rol_permiso,$this->rels_rol_permiso,'permiso_id','permiso_id');
        //Creando permisos nuevos
        foreach (count($rels_rol_permiso) > 0 ? $rels_rol_permiso : [] as $rel_nueva)
            RelRolPermiso::guardar(['rol_id' => $this->id, 'permiso_id' => $rel_nueva['permiso_id']]);
    }

    public function guardarUsuarios($usuarios_r) {
        $ids_nuevos = array_map(function ($item) { return $item['id']; }, $usuarios_r);
        foreach (count($this->usuarios) > 0 ? $this->usuarios : [] as $usuario_e) {
            $existente_arr = json_decode(json_encode($usuario_e), true);
            !in_array($existente_arr['id'], $ids_nuevos) ? $usuario_e->quitarRol() : null;
        }
        foreach (count($usuarios_r) > 0 ? $usuarios_r : [] as $usuario_r) {
            $usuario = Usuario::find($usuario_r['id']);
            $usuario->asignarRol($this->id);
        }
    }

//|------Funciones Estáticas------|//
    public static function guardar($arr_info) {
        $reg = isset($arr_info['id']) ? self::find($arr_info['id']) : new self();
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, []);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }
}
