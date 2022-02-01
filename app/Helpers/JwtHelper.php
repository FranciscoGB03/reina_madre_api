<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Log;


class JwtHelper extends Helper {
    private $configuration;

    /**
     * JwtHelper constructor.
     */
    public function __construct() {
        $this->configuration = Configuration::forSymmetricSigner(new Sha256(), InMemory::base64Encoded(config('custom.jwt_base64_key')));
    }


    public function generaAccessToken($user_id) {
        $expires_at = Carbon::now()->addMinutes(config('custom.minutes_atoken_exp'));
        $access_token = $this->generaToken($expires_at->toImmutable(), ['UID' => $user_id]);
        return $access_token;
    }

    public function generaRefreshToken($user_id) {
        $expires_at = Carbon::now()->addDays(config('custom.days_rtoken_exp'));
        $refresh_token = $this->generaToken($expires_at->toImmutable(), ['UID' => $user_id]);
        return $refresh_token;
    }

    public function generaSimpleToken($payload, $minutos_expira) {
        $expires_at = Carbon::now()->addMinutes($minutos_expira);
        $access_token = $this->generaToken($expires_at->toImmutable(), $payload);
        return $access_token;
    }

    public function esTokenVigente($string) {
        $token = $this->parsearToken($string);
        return !$token->isExpired(Carbon::now()->toDateTimeImmutable());
    }

    public function fueTokenAlterado($string) {
        $token = $this->parsearToken($string);
        return !$this->configuration->validator()->validate($token, new SignedWith($this->configuration->signer(), $this->configuration->signingKey()));
    }

    public function getClaims($string) {
        $token = $this->parsearToken($string);
        return $token->claims();
    }

    public function getUsuarioId($string) {
        $claims = $this->getClaims($string);
        $UID = $claims->get('UID');
        return $UID;
    }

    public function getUsuarioIdPlain($string) {
        $usuario_encriptado = $this->getUsuarioId($string);
        //Log::debug($usuario_encriptado);
        return Crypt::decrypt($usuario_encriptado);
    }

    public function getRequestToken() { return request()->get('token'); }

    public function esTokenValido($string) { return $this->esTokenVigente($string) && !$this->fueTokenAlterado($string); }

    private function parsearToken($string) {
        if (!isset($string))
            $string = "_._._";
        return $token = $this->configuration->parser()->parse($string);
    }

    private function generaToken($expires_at, $claims = []) {
        $token = $this->configuration->builder()
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(Str::random(20))
            ->issuedAt(Carbon::now()->toImmutable())
            ->expiresAt($expires_at);
        foreach ($claims as $key => $value)
            $token = $token->withClaim($key, $value);
        $token = $token->getToken($this->configuration->signer(), $this->configuration->signingKey())->toString();
        return $token;
    }

}
