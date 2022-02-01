<?php

namespace App\Models;

use App\Helpers\ModelsHelper;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class TipoConfiguracion extends Model {

    public $table = 'tipo_configuracion';
    public $fillable = ['nombre'];

//|------Attributes------|//
//|------Relaciones------|//
    public function configuraciones() { return $this->hasMany(Configuracion::class, 'tipo_configuracion_id'); }

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
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, []);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }
}
