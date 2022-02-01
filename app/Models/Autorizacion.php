<?php

namespace App\Models;

use App\Helpers\JwtHelper;
use App\Helpers\ModelsHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Log;
use Exception;

class Autorizacion extends Model {

    public $table = 'autorizacion';
    public $fillable = ['fecha', 'usuario_id', 'estatus', 'comentario'];

//|------Attributes------|//
//|------Relaciones------|//
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }

//|------Scopes------|//
    public function scopeActivo($q) { return $q; }

//|------Funciones de la clase------|//
    public function eliminar() { $this->delete(); }

    public function actualizar($arr_info) {
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['usuario_id']);
        $this->fill($info_guardar);
        $this->save();
        return $this;
    }

//|------Funciones Estáticas------|//
    public static function crearActual($estatus = null, $comentario = null) {
        $jwt_helper = new JwtHelper();
        $usuario_id = $jwt_helper->getUsuarioIdPlain($jwt_helper->getRequestToken());
        return self::guardar([
            'fecha' => Carbon::now()->toDateTimeString(),
            'usuario_id' => $usuario_id,
            'estatus' => $estatus,
            'comentario' => $comentario,
        ]);
    }

    public static function guardar($arr_info) {
        $reg = isset($arr_info['id']) ? self::find($arr_info['id']) : new self();
        $info_guardar = ModelsHelper::preparaParaGuardar($arr_info, ['usuario_id']);
        $reg->fill($info_guardar);
        $reg->save();
        return $reg;
    }
}
