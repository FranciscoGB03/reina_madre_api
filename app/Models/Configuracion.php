<?php

namespace App\Models;

use App\Helpers\ModelsHelper;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class Configuracion extends Model {

    public $table = 'configuracion';
    public $fillable = ['tipo_configuracion_id', 'nombre', 'descripcion', 'valor'];

//|------Attributes------|//
//|------Relaciones------|//
    public function tipo_configuracion() { return $this->belongsTo(TipoConfiguracion::class, 'tipo_configuracion_id'); }

//|------Scopes------|//
    public function scopePorNombre($q, $nombre) { return $q->where('nombre', 'like', $nombre); }

    public function scopeACtivo($q) { return $q; }

//|------Funciones de la clase------|//
    public function eliminar() { $this->delete(); }

    public function actualizar($arr_info) {
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, []);
        $this->fill($info_guardar);
        $this->save();
        return $this;
    }

//|------Funciones Estáticas------|//
    public static function getValor($nombre_configuracion) {
        $conf = self::porNombre($nombre_configuracion)->first();
        return isset($conf->id) ? $conf->valor : null;
    }

    public static function guardar($arr_info) {
        $reg = isset($arr_info['id']) ? self::find($arr_info['id']) : new self();
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['tipo_configuracion_id']);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }
}
