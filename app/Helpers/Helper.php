<?php

namespace App\Helpers;

use Carbon\Carbon;
use Log;

class Helper {
    private $log = [];
    const LOG_DEBUG = "DE";
    const LOG_INFO = "IN";
    const LOG_WARNING = "WA";
    const LOG_ERROR = "ER";
    const LOG_CRITICAL = "CR";

    public function loggea($texto, $mostrar_en_log = true, $severidad = self::LOG_DEBUG) {
        $texto = Carbon::now()->toDateTimeString() . " $texto";
        if ($mostrar_en_log)
            $this->escribeLog($texto, $severidad);
        $log[] = $texto;
    }

    public function getLog() { return $this->log; }

    private function escribeLog($texto, $severidad) {
        switch ($severidad) {
            case self::LOG_INFO:
                Log::info($texto);
            case self::LOG_WARNING:
                Log::warning($texto);
            case self::LOG_ERROR:
                Log::error($texto);
            case self::LOG_CRITICAL:
                Log::critical($texto);
            default:
                Log::debug($texto);
        }
    }
}
