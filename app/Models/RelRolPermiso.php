<?php

namespace App\Models;

use App\Helpers\ModelsHelper;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class RelRolPermiso extends Model {

    public $table = 'rel_rol_permiso';
    public $fillable = ['rol_id', 'permiso_id'];

//|------Attributes------|//
//|------Relaciones------|//
    public function permiso() { return $this->belongsTo(Permiso::class, 'permiso_id'); }

    public function rol() { return $this->belongsTo(Rol::class, 'rol_id'); }

//|------Scopes------|//
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
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['rol_id', 'permiso_id']);
        $reg = self::firstOrNew([
            'rol_id' => $info_guardar['rol_id'],
            'permiso_id' => $info_guardar['permiso_id'],
        ]);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }
}
