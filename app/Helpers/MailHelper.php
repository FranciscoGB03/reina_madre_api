<?php

namespace App\Helpers;

use App\Models\Configuracion;
use Log;
use Mail;

class MailHelper {
    public static function comentario($nombre, $correo, $mensaje) {
        $titulo = trans('mails.comentario');
        $destinatario = Configuracion::getValor('comentarios_sugerencias');
        $remitente = $correo;
        $body = $mensaje;
        Mail::send('mails.enviarComentario', compact('remitente', 'body', 'nombre'), function ($message) use ($destinatario, $titulo) { $message->to($destinatario)->subject($titulo); });
    }

    public static function olvideMiPassword($email, $hash) {
        $correos = self::getMessageTo($email);
        $correo = config('app.env') == 'dev' ? self::getMessageTo('softdev@inecti.com') : self::getMessageTo($correos);
        $titulo = trans('mails.sbRc');
        $url = config('custom.client_url') . "/usuario/olvidePassword/$hash";
        Mail::send('mails.restablecerPassword', compact('url'), function ($message) use ($correo, $titulo) { $message->to($correo)->subject($titulo); });
    }

    public static function ordenConfirmada($email, $transaccion) {
        $correos = self::getMessageTo($email);
        $correo = config('app.env') == 'dev' ? self::getMessageTo('softdev@inecti.com') : self::getMessageTo($correos);
        $titulo = trans('mails.ordenConfirmada');
        $url = config('custom.client_url') . "/transaccion/ver/$transaccion->uuid";
        Mail::send('mails.ordenConfirmada', compact('transaccion', 'url'), function ($message) use ($correo, $titulo) { $message->to($correo)->subject($titulo); });
    }

    public static function tranEnTransito($transaccion) {
        $email = $transaccion->comprador->email;
        $subject = trans('mails.sbEnTransito');
        $titulo = trans('mails.tiEnTransito');
        $texto = trans('mails.txEnTransito');
        self::transaccion($email, $transaccion, $subject, $titulo, $texto);
    }
    public static function tranPagoRechazado($transaccion) {
        $email = $transaccion->comprador->email;
        $subject = trans('mails.sbPagoRechazado');
        $titulo = trans('mails.tiPagoRechazado');
        $texto = trans('mails.txPagoRechazado');
        self::transaccion($email, $transaccion, $subject, $titulo, $texto);
    }
    public static function tranPagoValidado($transaccion) {
        $email = $transaccion->comprador->email;
        $subject = trans('mails.sbPagoValidado');
        $titulo = trans('mails.tiPagoValidado');
        $texto = trans('mails.txPagoValidado');
        self::transaccion($email, $transaccion, $subject, $titulo, $texto);
    }
    public static function tranRecibido($transaccion) {
        $email = Configuracion::getValor('correo_recibido');
        $subject = trans('mails.sbRecibido');
        $titulo = trans('mails.tiRecibido');
        $texto = trans('mails.txRecibido');
        self::transaccion($email, $transaccion, $subject, $titulo, $texto);
    }

    public static function transaccion($email, $transaccion, $subject, $titulo, $texto1) {
        $correos = self::getMessageTo($email);
        $correo = config('app.env') == 'dev' ? self::getMessageTo('softdev@inecti.com') : self::getMessageTo($correos);
        $url = config('custom.client_url') . "/transaccion/ver/$transaccion->uuid";
        Mail::send('mails.transaccion', compact('titulo', 'transaccion', 'texto1', 'url'),
            function ($message) use ($correo, $titulo, $subject) { $message->to($correo)->subject($subject); });
    }

    public static function verificarCuenta($email, $hash) {
        $correos = self::getMessageTo($email);
        $correo = config('app.env') == 'dev' ? self::getMessageTo('softdev@inecti.com') : self::getMessageTo($correos);
        $titulo = trans('mails.sbAc');
        $url = config('custom.client_url') . "/usuario/activarCuenta/$hash";
        Mail::send('mails.activarCuenta', compact('url'), function ($message) use ($correo, $titulo) { $message->to($correo)->subject($titulo); });
    }

//----------Miscelaneas--------
    public static function getMessageTo($direccion) {
        $direccion = is_string($direccion) ? str_replace(' ', '', $direccion) : $direccion;
        if (is_array($direccion) || !strpos($direccion, ','))
            return $direccion;
        else
            return explode(",", $direccion);
    }
}
