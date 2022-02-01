<?php

namespace App\Models;

use App\Helpers\ModelsHelper;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class RelDepartamentoEmpresa extends Model {
    public $table = 'rel_departamento_empresa';
    public $fillable = ['empresa_id', 'departamento_id','usuario_id'];
//|------Attributes------|//
//|------Relaciones------|//
    public function empresa() { return $this->belongsTo(Empresa::class, 'empresa_id'); }
    public function departamento() { return $this->belongsTo(Departamento::class, 'departamento_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }

//|------Scopes------|//
    public function scopeActivo($q) { return $q; }
//|------Funciones de la clase------|//
    public function eliminar() { $this->delete(); }
    public function actualizar($arr_info) {
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['empresa_id','departamento_id','usuario_id']);
        $this->fill($info_guardar);
        $this->save();
        return $this;
    }
//|------Funciones Estáticas------|//
    public static function guardar($arr_info) {
        $reg = isset($arr_info['id']) ? self::find($arr_info['id']) : new self();
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['empresa_id','departamento_id','usuario_id']);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }
}
