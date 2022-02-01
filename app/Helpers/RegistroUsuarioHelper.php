<?php


namespace App\Helpers;


use App\Models\Configuracion;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Exception;
use Log;

class RegistroUsuarioHelper extends Helper {
    private $usuario = null;

    const CORRECTO = "ok";
    const EXPIRADO = "error.hashExpirado";
    const DESCONOCIDO = "error.desconocido";

    public function registrar($email, $password, $info_personal_id) {
        $this->guardarUsuario($email, $password, $info_personal_id);
        $hash = $this->getHash();
        MailHelper::verificarCuenta($email, $hash);
    }

    private function guardarUsuario($email, $password, $info_personal_id) {
        if (!UtilsHelper::esEmail($email))
            throw new Exception("error:emailIncorrecto");
        $this->usuario = Usuario::guardar([
            'rol_id' => Configuracion::getValor('default_rol_id'),
            'estatus' => Usuario::STATUS_NUEVO,
            'email' => $email,
            'fecha_verificacion' => null,
            'password' => Hash::make($password),
            'ultimo_login' => null,
            'info_personal_id' => $info_personal_id
        ]);
    }

    private function getHash() {
        $minutos_vigencia = Configuracion::getValor('minutos_vigencia_hash');
        $fecha_caducidad = Carbon::now()->addMinutes($minutos_vigencia);
        $usuario_id = $this->usuario->id;
        return Crypt::encrypt("xheim###" . $fecha_caducidad->toDateTimeString() . "###" . $usuario_id . "###xheim");
    }

    public function validarHash($hash) {
        $result = ['es_valido' => true, 'partes' => [], 'es_vigente' => true];
        $result['partes'] = $this->getPartesDeHash($hash);
        $total_partes = 4;
        for ($i = 0; $i < $total_partes; $i++)
            $result['es_valido'] = $result['es_valido'] && isset($result['partes'][$i]);
        if ($result['es_valido']) {
            $ahora = Carbon::now();
            $fecha_hash = Carbon::parse($result['partes'][1]);
            $result['es_vigente'] = $ahora->lte($fecha_hash);
        } else
            $result['es_vigente'] = false;
        return $result;
    }

    public function activarCuenta($hash) {
        $validacion = $this->validarHash($hash);
        if (!$validacion['es_valido'])
            throw new Exception('error.hashActivacionAlterado');
        if (!$validacion['es_vigente'])
            return self::EXPIRADO;
        Usuario::find($validacion['partes'][2])->activar();
        return self::CORRECTO;
    }

    private function getPartesDeHash($hash) {
        $plain = Crypt::decrypt($hash);
        return explode("###", $plain);
    }

    public function enviaMailPassword($usuario) {
        $this->usuario = $usuario;
        $hash = $this->getHash();
        MailHelper::olvideMiPassword($this->usuario->email, $hash);
    }

    public function restablecerPassword($usuario_id, $nuevo_password) {
        $this->usuario = Usuario::find($usuario_id);
        $this->usuario->actualizar(['password' => Hash::make($nuevo_password)]);
    }
}
