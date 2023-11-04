<?php

namespace Middleware;

class Auth{

    static function VerifyAuthenication(): bool
    {
        # Read Authorization header or empty string
        $token = getallheaders()['Authorization'] ?? '';
        
        # Return if $token is equal to authorization token
        return $token === ACCES_TOKEN;
        // return in_array($host, ALLOWED_HOSTS) || $token === ACCES_TOKEN;
    }
}