<?php

namespace App\Models;

use App\Helpers\ModelsHelper;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class Adjunto extends Model {

    public $table = 'adjunto';
    public $fillable = ['nombre', 'url'];

//|------Attributes------|//
//|------Relaciones------|//
//|------Scopes------|//
//|------Funciones de la clase------|//
    public function eliminar() {
        foreach ($this->traducciones ?? [] as $traduccion)
            $traduccion->eliminar();
        foreach ($this->subcategorias ?? [] as $subcategoria)
            $subcategoria->eliminar();
        $this->delete();
    }

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
