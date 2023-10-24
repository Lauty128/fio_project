<?php

namespace Middleware;

class Auth{

    static function VerifyAuthenication(): bool
    {
        $token = getallheaders()['authorization'] ?? '';
        return $token === ACCES_TOKEN;
        // return in_array($host, ALLOWED_HOSTS) || $token === ACCES_TOKEN;
    }
}