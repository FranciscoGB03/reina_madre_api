<?php


namespace App\Http\Controllers;


use App\Helpers\MailHelper;

class ComentariosController extends Controller{

    public function enviarMensaje(){
        try {
            $nombre = request()->get('nombre');
            $correo = request()->get('correo');
            $mensaje = request()->get('mensaje');
            MailHelper::comentario($nombre,$correo,$mensaje);
            return parent::returnJsonSuccess("ok");
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }
}
