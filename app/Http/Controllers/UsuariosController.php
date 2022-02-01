<?php

namespace App\Http\Controllers;

use App\Constants\PedidosConst;
use App\Helpers\BuscadorUsuariosHelper;
use App\Helpers\JwtHelper;
use App\Helpers\RegistroUsuarioHelper;
use App\Models\Categoria;
use App\Models\Direccion;
use App\Models\InfoPersonal;
use App\Models\Usuario;
use DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Log;

class UsuariosController extends Controller {

    public function actualizar() {
        try {
            $usuario_id = request('usuario_id');
            $info_actualizar = request('info_actualizar');
            $usuario = Usuario::find($usuario_id);
            if (isset($info_actualizar['password']))
                unset($info_actualizar['password']);
            $usuario->actualizar($info_actualizar);
            return parent::returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function activarCuenta() {
        try {
            $hash = request('hash');
            $helper = new RegistroUsuarioHelper();
            $result = $helper->activarCuenta($hash);
            return parent::returnJsonSuccess(compact('result'));
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function cambiarPassword() {
        try {
            $usuario_id = request('usuario_id');
            $password = request('password');
            $usuario = Usuario::find($usuario_id);
            if (!isset($usuario->id))
                throw  new Exception('error:usuarioNoExiste');
            $usuario->actualizar(['password' => Hash::make($password)]);
            return parent::returnJsonSuccess(compact($usuario));
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function consultar() {
        try {
            $order = request('order');
            $filtros = request('filtros');
            $pagina = request('pagina');
            $rows = request('rows');
            $relaciones = request('relaciones');
            $helper = new BuscadorUsuariosHelper($order, $filtros, $pagina, $rows, $relaciones);
            $registros = $helper->consultar();
            return $this->returnJsonSuccess($registros);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function eliminarUsuario() {
        try {
            $usuario_id = request('usuario_id');
            $usuario = Usuario::find($usuario_id);
            if (!isset($usuario->id))
                throw  new Exception('error:usuarioNoExiste');
            $usuario->eliminar();
            return $this->returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function getAllUsuarios() {
        try {
            $registros = Usuario::with(['rol'])->get();
            return $this->returnJsonSuccess($registros);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function getMiPerfil() {
        try {
            $usuario_id_e = request('usuario_id_e');
            $usuario_id = Crypt::decrypt($usuario_id_e);
            $relaciones = request('relaciones');
            $result = Usuario::porId($usuario_id);
            if (isset($relaciones))
                $result = $result->with($relaciones);
            return $this->returnJsonSuccess($result->first());
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function actualizarFotoPerfil() {
        try {
            $foto_uuid = request()->get('foto_uuid');
            $info_personal_id = request()->get('info_personal_id');
            $info = InfoPersonal::find($info_personal_id);
            Log::debug($info);
            $info['foto_uuid'] = $foto_uuid;
            $info->save();
            return $this->returnJsonSuccess($info);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }


    public function guardarDireccion() {
        try {
            $usuario_id_e = request('usuario_id_e');
            $usuario_id = Crypt::decrypt($usuario_id_e);
            $es_primera = request('es_primera');
            $es_vendedor = request('es_vendedor');
            $direccion_r = request('direccion');
            $usuario = Usuario::find($usuario_id);
            Direccion::crearDireccion($usuario->info_personal_id, $es_primera, $es_vendedor, $direccion_r);
            return $this->returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function olvidePassword() {
        try {
            $email = request('email');
            $usuario = Usuario::porEmail($email)->first();
            if (!isset($usuario->id))
                return "";
//                throw new Exception("error.correoNoExiste");
            $helper = new RegistroUsuarioHelper();
            $helper->enviaMailPassword($usuario);
            return parent::returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function rechazarEliminarDireccion() {
        try {
            $direccion_id = request('direccion_id');
            $direccion = Direccion::find($direccion_id);
            $direccion->eliminar();
            return parent::returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function registrarUsuario() {
        try {
            return DB::transaction(function () {
                $usuario_r = request()->get('usuario');
                $password = null;
                if (!isset($usuario_r['id'])) {
                    $password = Usuario::generaPassword();
                    $usuario_r['password'] = Hash::make($password);
                    $usuario_r['estatus'] = 'ACT';
                }
                $usuario = Usuario::guardar($usuario_r);
                return parent::returnJsonSuccess(compact('usuario', 'password'));
            });
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function restablecerPassword() {
        try {
            return DB::transaction(function () {
                $hash = request()->get('hash');
                $nuevo_password = request()->get('nuevo_password');
                $helper = new RegistroUsuarioHelper();
                $validacion = $helper->validarHash($hash);
                if (!$validacion['es_valido'])
                    throw new Exception('error.hashAlterado');
                $helper->restablecerPassword(intval($validacion['partes'][2]), $nuevo_password);
                return parent::returnJsonSuccess("ok");
            });
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function validarHash() {
        try {
            $hash = request('hash');
            $validacion = (new RegistroUsuarioHelper())->validarHash($hash);
            $result = $validacion['es_valido'];
            return parent::returnJsonSuccess(compact('result'));
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }
}
