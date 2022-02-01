<?php


namespace App\Http\Controllers;
use Exception;
use Log;

class CatalogosController extends Controller {
//|------Proceso en Batch------|//
    public function getMultiAllGenerico() {
        try {
            $peticiones = request()->get('peticiones');
            if ($peticiones == null || !is_array($peticiones))
                throw  new Exception('No se recibió el arreglo de peticiones o no es de tipo arreglo');
            $responses = $this->consultaGenericos($peticiones);
            return parent::returnJsonSuccess($responses);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function getMultiActivosGenerico() {
        try {
            $peticiones = request()->get('peticiones');
            if ($peticiones == null || !is_array($peticiones))
                throw  new Exception('No se recibió el arreglo de peticiones o no es de tipo arreglo');
            $responses = $this->consultaGenericos($peticiones, true);
            return parent::returnJsonSuccess($responses);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    private function consultaGenericos($peticiones, $solo_activos = false) {
        $responses = [];
        foreach (count($peticiones) > 0 ? $peticiones : [] as $peticion) {
            $response = ['status' => 'pend', 'data' => [], 'nombre' => $peticion['nombre']];
            try {
                $registros = $this->consultaGenerico($peticion['nombre'], $peticion['relaciones'], $solo_activos);
                $response['status'] = 'success';
                $response['data'] = $registros;
            } catch (Exception $ex) {
                Log::error($ex->getTraceAsString());
                $response['status'] = 'error';
                $response['data'] = [];
            }
            $responses[$peticion['nombre']] = $response;
        }
        return $responses;
    }

//|------Genéricos individuales------|//
    public function eliminarGenerico($clave) {
        try {
            $registro_id = request('registro_id');
            $key = $this->camposPorClave($clave);
            $this->verificarKey($clave, $key, 'eliminar');
            $registro = call_user_func('App\Models\\' . $key['modelo'] . '::find', $registro_id);
            $registro->eliminar();
            return parent::returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function getAllGenerico($clave) {
        try {
            $relaciones = request('relaciones') != null && count(request('relaciones')) > 0 ? request('relaciones') : [];
            $registros = $this->consultaGenerico($clave, $relaciones);
            return parent::returnJsonSuccess($registros);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function consultaGenerico($clave, $relaciones, $solo_activos = false) {
        $key = $this->camposPorClave($clave);
        $this->verificarKey($clave, $key, 'all');
        $registros = call_user_func('App\Models\\' . $key['modelo'] . '::orderBy', $key['order_by']);
        if ($solo_activos)
            $registros = $registros->activo();
        $relaciones != null && count($relaciones) > 0 ? $registros = $registros->with($relaciones) : null;
        return $registros->get();
    }

    public function getActivosGenerico($clave) {
        try {
            $relaciones = request('relaciones') != null && count(request('relaciones')) > 0 ? request('relaciones') : [];
            $registros = $this->consultaGenerico($clave, $relaciones, true);
            return parent::returnJsonSuccess($registros);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }


    public function getGenerico($clave) {
        try {
            $key = $this->camposPorClave($clave);
            $this->verificarKey($clave, $key, 'get');
            if (request()->get('id') == null)
                throw new Exception(trans('excepciones.propiedadNoEncontrada', ['nombre' => 'id']));
            $id = request()->get('id');
            $registro = call_user_func('App\Models\\' . $key['modelo'] . '::where', 'id', $id);
            request('relaciones') !== null ? $registro = $registro->with(request('relaciones')) : null;
            return parent::returnJsonSuccess($registro->first());
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function getGenericoPorUuid($clave) {
        try {
            $key = $this->camposPorClave($clave);
            $this->verificarKey($clave, $key, 'get');
            if (request()->get('uuid') == null)
                throw new Exception(trans('excepciones.propiedadNoEncontrada', ['nombre' => 'uuid']));
            $uuid = request()->get('uuid');
            $registro = call_user_func('App\Models\\' . $key['modelo'] . '::where', 'uuid', $uuid);
            request('relaciones') !== null ? $registro = $registro->with(request('relaciones')) : null;
            return parent::returnJsonSuccess($registro->first());
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function getPorCampo($clave) {
        try {
            $key = $this->camposPorClave($clave);
            $this->verificarKey($clave, $key, 'get');
            if (request()->get('campo') == null)
                throw new Exception(trans('excepciones.propiedadNoEncontrada', ['nombre' => 'campo']));
            if (request()->get('valor') == null)
                throw new Exception(trans('excepciones.propiedadNoEncontrada', ['nombre' => 'valor']));
            $campo = request()->get('campo');
            $valor = request()->get('valor');
            $registro = call_user_func('App\Models\\' . $key['modelo'] . '::where', $campo, $valor);
            request('relaciones') !== null ? $registro = $registro->with(request('relaciones')) : null;
            return parent::returnJsonSuccess($registro->first());
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function guardarGenerico($clave) {
        try {
            $key = $this->camposPorClave($clave);
            $this->verificarKey($clave, $key, 'guardar');
            $info = request()->all();
            return parent::returnJsonSuccess(
                call_user_func('App\Models\\' . $key['modelo'] . '::guardar', $info)
            );
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function verificarKey($clave, $key, $metodo) {
        if ($key['modelo'] == null)
            throw new Exception(trans('excepciones.claveNoExiste', ['clave' => $clave]));
        if (!$key[$metodo])
            throw new Exception(trans('excepciones.metodoNoPermitido', ['metodo' => $metodo, 'modelo' => $key['modelo']]));
    }

    public function camposPorClave($clave) {
        $arr = [
            'configuracion' => ['modelo' => 'Configuracion', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
            'permiso' => ['modelo' => 'Permiso', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
            'rol' => ['modelo' => 'Rol', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
            'seccion_permiso' => ['modelo' => 'SeccionPermiso', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
            'tipo_configuracion' => ['modelo' => 'TipoConfiguracion', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
            'usuario'=>['modelo'=>'Usuario', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
            'departamento'=>['modelo'=>'Departamento', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
            'empresa'=>['modelo'=>'Empresa', 'order_by' => 'nombre', 'all' => true, 'get' => true, 'guardar' => true, 'eliminar' => true],
        ];
        if (!isset($arr[$clave]))
            throw new Exception(trans('excepciones.claveNoExiste', ['clave' => $clave]));
        return $arr[$clave];
    }
}
