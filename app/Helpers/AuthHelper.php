<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthHelper extends Helper {
    private $usuario;

    public function __construct($usuario = null) { $this->usuario = $usuario; }

    public function esPasswordCorrecta($password) { return Hash::check($password, $this->usuario->password); }

    public function encriptaUsuarioId() { return Crypt::encrypt($this->usuario->id); }

    public function desencriptaUsuarioId() { return Crypt::decrypt($this->usuario->id); }

}
