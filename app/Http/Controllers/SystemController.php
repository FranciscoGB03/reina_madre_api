<?php

namespace App\Http\Controllers;

use App\Constants\PedidosConst;
use App\Helpers\JwtHelper;
use App\Models\RelRolUsuario;
use Artisan;
use DB;
use Exception;
use Log;

class SystemController extends Controller {
    public function configCache() {
        Artisan::call('config:cache');
        dd(Artisan::output());
    }

    public function migrationPretend() {
        Artisan::call('migrate:fresh --seed');
        dd(Artisan::output());
    }

    public function version() {
        return "<h1>v1.0.0-rc-1</h1>";
    }
}
