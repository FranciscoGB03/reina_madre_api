<?php

namespace App\Http\Controllers;

use App\Constants\PedidosConst;
use App\Helpers\JwtHelper;
use App\Models\RelRolUsuario;
use App\Models\Usuario;
use DB;
use Exception;
use Log;

class SeguridadController extends Controller {
    public function getPermisos() {
        try {
            $usuario_id = (new JwtHelper())->getUsuarioIdPlain(request()->get('token'));
            $usuario = Usuario::porId($usuario_id)->first();
            $rels = $usuario->rol->rels_rol_permiso;
            $permisos = [];
            if(count($rels) > 0)
                $permisos = $usuario->rol->rels_rol_permiso->load('permiso.seccion_permiso')->pluck('permiso');
            return parent::returnJsonSuccess($permisos);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }
}
