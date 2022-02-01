<?php


namespace App\Http\Controllers;
use App\Models\RelDepartamentoEmpresa;

class RelDepartamentoEmpresaController extends Controller {
    public function guardarDepartamentoEmpresa() {
        try{
            $rel_departamento_r=request('rel_departamento_empresa');
            $rel_departamento=RelDepartamentoEmpresa::guardar($rel_departamento_r);
            return parent::returnJsonSuccess($rel_departamento);

        }catch(Exception $ex){
            return parent::returnJsonError($ex);
        }
    }
    public function consultar() {
        try {
            $registros=RelDepartamentoEmpresa::with(['empresa','departamento','usuario'])->get();
            return parent::returnJsonSuccess($registros);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

}
