<?php

namespace App\Models;

use App\Helpers\ModelsHelper;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class Permiso extends Model {

    public $table = 'permiso';
    public $fillable = ['nombre', 'descripcion', 'seccion_permiso_id'];

//|------Attributes------|//
//|------Relaciones------|//
    public function rels_rol_permiso() { return $this->hasMany(RelRolPermiso::class, 'permiso_id'); }

    public function seccion_permiso() { return $this->belongsTo(SeccionPermiso::class, 'seccion_permiso_id'); }


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

//|------Funciones Estáticas------|//
    public static function guardar($arr_info) {
        $reg = isset($arr_info['id']) ? self::find($arr_info['id']) : new self();
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['seccion_permiso_id']);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }
}
