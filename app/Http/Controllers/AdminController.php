<?php

namespace App\Http\Controllers;

use App\Constants\PedidosConst;
use App\Helpers\CpHelper;
use App\Models\Articulo;
use App\Models\Configuracion;
use App\Models\Enlace;
use App\Models\Rol;
use App\Models\Visita;
use Carbon\Carbon;
use DB;
use Exception;
use Log;

class AdminController extends Controller {


    public function getConfiguraciones() {
        try {
            $configuraciones_r = request()->get('configuraciones');
            $configuraciones = [];
            foreach ($configuraciones_r ?? [] as $nombre)
                $configuraciones[] = Configuracion::porNombre($nombre)->first();
            return parent::returnJsonSuccess($configuraciones);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function getConfiguracionesActivas() {
        try {
            $configs = Configuracion::activo();
            return parent::returnJsonSuccess($configs->get(['nombre', 'valor']));
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }


    public function guardarRol() {
        try {
            return DB::transaction(function () {
                $rol_id = request()->get('rol_id');
                $permisos = request()->get('permisos');
                $rol = Rol::find($rol_id);
                if (!isset($rol->id))
                    throw new Exception('error.rolDesconocido');
                $rol->guardarPermisos($permisos);
                return parent::returnJsonSuccess($rol);
            });
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function guardarConfiguraciones() {
        try {
            return DB::transaction(function () {
                $configuraciones = request('configuraciones');
                foreach (($configuraciones ?? []) as $conf_r) {
                    $configuracion = Configuracion::find($conf_r['id']);
                    $configuracion->actualizar(['valor' => $conf_r['valor']]);
                }
                return parent::returnJsonSuccess("ok");
            });
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }
    public function guardarRolPermisos() {
        try {
            $rol = Rol::find(request()->get('rol')['id']);
            $rol->guardarPermisos(request()->get('rol')['rels_rol_permiso']);
            return parent::returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

}
