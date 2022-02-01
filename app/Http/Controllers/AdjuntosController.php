<?php

namespace App\Http\Controllers;

use App\Helpers\FilesHelper;
use App\Helpers\UtilsHelper;
use App\Models\Adjunto;
use App\Models\Propiedad;
use App\Models\RelCategoriaAdjunto;
use App\Models\RelSubcategoriaAdjunto;
use App\Models\Usuario;
use Exception;
use Log;
use Storage;
use Illuminate\Http\Response as HttpResponse;

class AdjuntosController extends Controller {
    const PROPIEDAD = 'PROP';

//|------REST Services------|//
    public function guardarContratoPropiedad() {
        try {
            $this->guardarAdjuntos(self::PROPIEDAD, ['propiedad_id' => request('propiedad_id')]);
            return parent::returnJsonSuccess(['result' => 'ok']);
        } catch (Exception $ex) {
            return parent::returnJsonError($ex);
        }
    }

    public function guardarAdjuntos($tipo, $data = null) {
        Log::debug(request()->all());
        Log::debug(request()->file('adjuntos'));
        foreach (count(request()->file('adjuntos')) > 0 ? request()->file('adjuntos') : [] as $indice => $adjunto) {
            if (get_class($adjunto) == 'Illuminate\Http\UploadedFile') {
                $nuevo_nombre = "$tipo-" . substr($adjunto->getClientOriginalName(), -50);
                $adjunto_model = Adjunto::guardar([
                    'nombre' => $nuevo_nombre,
                    'url' => $nuevo_nombre,
                ]);
                switch ($tipo) {
                    case self::PROPIEDAD:
                        Propiedad::find($data['propiedad_id'])->actualizar(['contrato_id' => $adjunto_model->id]);
                        break;
                }
                $adjunto->storeAs($tipo, $nuevo_nombre);
            } else
                Log::error("El archivo no es del tipo UploadedFile");
        }
    }

    public function descargar($adjunto_id) {
        $adjunto = Adjunto::find($adjunto_id);
//        Log::debug($adjunto);
        if (isset($adjunto->id)) {
            $fs = Storage::getDriver();
            $file = $adjunto->id . "-" . $adjunto->url;
            $stream = $fs->readStream($file);
            return response()->stream(function () use ($stream) {
                fpassthru($stream);
            }, HttpResponse::HTTP_OK, [
                "Content-Type" => $fs->getMimetype($file),
                "Content-Length" => $fs->getSize($file),
                "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
            ]);
        } else {
            throw new Exception(trans('excepciones.archivoNoEncontrado', ['ruta' => $adjunto->url]));
        }
    }
}
