<?php

namespace App\Http\Controllers;

use App\Constants\PedidosConst;
use DB;
use Exception;
use Log;

class EjemploController extends Controller {
//|------Nombre de mi modulo------|//
    public function getDevueltosPorFechas() {
        try {
            $relaciones = request()->get('relaciones');
            $fecha_inicio = request()->get('fecha_inicio');
            $fecha_fin = request()->get('fecha_fin');
            return parent::returnJsonSuccess("aquí mando ");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }
}
