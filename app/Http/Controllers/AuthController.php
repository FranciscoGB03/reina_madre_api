<?php

namespace App\Http\Controllers;

use App\Constants\PedidosConst;
use App\Helpers\AuthHelper;
use App\Helpers\JwtHelper;
use App\Models\Usuario;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Log;
use Str;

class AuthController extends Controller {
    public function attempt() {
        try {
            $email_c = request()->get('email');
            $password_c = request()->get('password');
            $email = $this->decifraDeFront($email_c);
            $password = $this->decifraDeFront($password_c);
            $usuario = Usuario::porEmail($email)->first();
            if (!isset($usuario->id)) //Si no se encuentra usuario en la BD
                return parent::returnJsonCodigoError('auth:usuarioInexistente', parent::HTTP_FORBIDDEN);
            $auth_helper = new AuthHelper($usuario);
            if (!$auth_helper->esPasswordCorrecta($password)) //Si la contraseña no es correcta
                return parent::returnJsonCodigoError('auth:passwordIncorrecta', parent::HTTP_FORBIDDEN);
            $jwt_helper = new JwtHelper();
            $access_token = $jwt_helper->generaAccessToken($auth_helper->encriptaUsuarioId());
            $refresh_token = $jwt_helper->generaRefreshToken($auth_helper->encriptaUsuarioId());
            $usuario_info = ['nombre' => $usuario->nombre];
            $usuario->actualizar(['ultimo_login' => Carbon::now()->toDateTimeString()]);
            $enc_key = Str::random(16);
            return parent::returnJsonSuccess(compact('access_token', 'refresh_token', 'usuario_info', 'enc_key'));
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function renuevaAccessToken() {
        try {
            $refresh_token = request()->get('refresh_token');
            $jwt_helper = new JwtHelper();
            if (!$jwt_helper->esTokenValido($refresh_token))
                throw new Exception("Invalid refresh token");
            $access_token = $jwt_helper->generaAccessToken($jwt_helper->getUsuarioId($refresh_token));
            return parent::returnJsonSuccess(compact('access_token'));
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    private function decifraDeFront($string_c) {
        $descifrado = base64_decode($string_c);
        $partes = explode("##", $descifrado);
        if (count($partes) != 3)
            throw new Exception('error:stringMalFormada');
        return $partes[1];
    }

}
