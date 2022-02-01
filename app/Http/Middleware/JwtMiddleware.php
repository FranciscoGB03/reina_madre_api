<?php

namespace App\Http\Middleware;

use App\Helpers\JwtHelper;
use App\Helpers\JwtTokenHelper;
use Closure;
use Illuminate\Http\Response as HttpResponse;
use Log;

class JwtMiddleware {
    public function handle($request, Closure $next, $guard = null) {
        $arr = explode(" ", $request->header('Authorization'));
        if (!isset($arr[1]))
            return response()->json(['mensaje' => 'error.sinToken'], HttpResponse::HTTP_FORBIDDEN);
        $token = $arr[1];
        $helper = new JwtHelper();
        if ($helper->fueTokenAlterado($token))
            return response()->json(['mensaje' => 'error.tokenAlterado'], HttpResponse::HTTP_FORBIDDEN);
        if (!$helper->esTokenVigente($token))
            return response()->json(['mensaje' => 'error.tokenExpirado'], HttpResponse::HTTP_FORBIDDEN);
        $request->merge(['token' => $token]);
        return $next($request);

    }
}
