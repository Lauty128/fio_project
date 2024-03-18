<?php

namespace App\Middleware;

use App\Config\Config;

class Auth{

    static function VerifyAuthenication(): bool
    {
        // Se obtiene el valor de la cabecera "Authorization" y si no existe tomamos ""
        $token = getallheaders()['Authorization'] ?? '';
        
        // Retorna un booleano que indica si el token de acceso es correcto
        return $token === Config::ACCESS_TOKEN;

        // Codigo pensado para permitir a ciertos dominios el acceso
        // return in_array($host, ALLOWED_HOSTS) || $token === ACCES_TOKEN;
    }
}