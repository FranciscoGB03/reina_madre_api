<?php

namespace App\Http\Controllers;

use App\Constants\PedidosConst;
use App\Helpers\JwtHelper;
use App\Models\Transaccion;
use App\Models\Usuario;
use DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Log;

class DevController extends Controller {
//|------Nombre de mi modulo------|//
    public function crearUsuario($email, $pass_text) {
        try {
            $password = Hash::make($pass_text);
            $usuario = Usuario::guardar(compact('email', 'password'));
            return "Usuario creado con id $usuario->id";
        } catch (Exception $ex) {
            return $ex;
        }
    }

    public function fueTokenAlterado($token) {
        return (new JwtHelper())->getClaims($token);
    }

    public function activarCuenta($hash) {
        return view('mails.activarCuenta', compact('hash'));
    }

    public function test($cadena) {
        return Crypt::decrypt($cadena);
    }

    public function ordenConfirmada($transaccion_id) {
        $transaccion = Transaccion::find($transaccion_id);
        $url = config('custom.client_url') . "/transaccion/ver/$transaccion->uuid";
        return view('mails.ordenConfirmada', compact('transaccion', 'url'));
    }
}
