<?php

namespace App\Middleware;

use App\Config\Config;

class Auth{

    static function VerifyAuthenication(): bool
    {
        $token = getallheaders()['Authorization'] ?? '';
        return $token === Config::ACCESS_TOKEN;
        // return in_array($host, ALLOWED_HOSTS) || $token === ACCES_TOKEN;
    }
}