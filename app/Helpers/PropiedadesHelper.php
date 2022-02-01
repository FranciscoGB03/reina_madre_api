<?php


namespace App\Helpers;


use App\Models\Propiedad;
use Exception;

class PropiedadesHelper extends Helper {

    public function validaParaGuardar($propiedad_r) {
        $propiedad = isset($propiedad_r['id']) ? Propiedad::find($propiedad_r['id']) : null;
        if (!isset($propiedad_r['subcategoria_id']) && !isset($propiedad_r['subcategoria']) && !isset($propiedad_r['subcategoria']['id']))
            throw new Exception("error:sinSubcategoria");
        if (!isset($propiedad_r['nombre']))
            throw new Exception("error:sinNombre");
        if (isset($propiedad->id) && isset($propiedad_r['estatus']) && $propiedad_r['estatus'] != $propiedad->estatus)//Si hay un cambio de estatus directo
            throw new Exception("error:cambioEstatusNoPermitido");
        return true;
    }
}
