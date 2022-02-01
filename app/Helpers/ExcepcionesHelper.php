<?php

namespace App\Helpers;

use Exception;
use Log;

class ExcepcionesHelper {

    /**
     * Agrega a un arreglo que retorna las propiedades que no haya encontrado en
     * el arreglo solicitado
     * @param array $arr_info arreglo en el que se verificarán las propiedades
     * @param array $propiedades Propiedades que se desean buscar
     * @return array propiedades que no fueron encontradas
     */
    public static function tienePropiedades($arr_info, $propiedades) {
        $propiedades_unset = [];
        foreach ($propiedades as $prop) {
            isset($arr_info[$prop]) ? null : $propiedades_unset[] = $prop;
        }
        return $propiedades_unset;
    }

    public static function tienePropiedadesOpcionales($arr_info, $propiedades) {
        $tiene_propiedades = false;
        foreach ($propiedades as $prop) {
            isset($arr_info[$prop]) ? $tiene_propiedades = true : null;
        }
        return $tiene_propiedades;
    }

    /**
     * Valida que las propiedades existan en el arreglo enviado
     * @param type $arr_info arreglo en el que se verificarán las propiedades
     * @param type $propiedades Propiedades que se desean buscar
     */
    public static function validaPropiedades($arr_info, $propiedades) {
        $errores = self::tienePropiedades($arr_info, $propiedades);
        if (count($errores) > 0) {
            throw new Exception(trans('informacionNoEncontrada', ['nombre' => implode($errores, ',')]), 1);
        }
    }

    public static function validaPropiedadesOpcionales($arr_info, $propiedades) {
        $result = self::tienePropiedadesOpcionales($arr_info, $propiedades);
        if (!$result) {
            throw new Exception(trans('informacionNoEncontrada', ['nombre' => implode($propiedades, ',')]), 1);
        }
    }

    public static function error($ex) {
        Log::error($ex->getFile() . " " . $ex->getLine() . " " . $ex->getMessage() . "\n" . $ex->getTraceAsString());
    }

    public static function getMessageFromArr($arr) {

    }

}
