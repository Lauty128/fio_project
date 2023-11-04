<?php


# ----------- CONFIGURAR CORS
    /*
    Cuando se envían encabezados personalizados, el navegador realiza una solicitud de preflight (OPTIONS) antes de la solicitud real. 
    Asegúrate de que tu servidor PHP responda adecuadamente a estas solicitudes OPTIONS.
    */
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        // ESTO SOLUCIONA UN ERROR QUE SUCEDE AL ENVIAR CABECERAS DE AUTENTICACION
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Credentials: true");
        header("Content-Length: 0");
        header("Content-Type: text/plain");
        exit();
    }

    // Specify domains from which requests are allowed
    header("Access-Control-Allow-Origin: *");
        // Specify which request methods are allowed
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    // Additional headers which may be sent along with the CORS request
    header("Access-Control-Allow-Headers: Content-Type, Authorization");


//---------> Definir timezone
    date_default_timezone_set("America/Argentina/Buenos_Aires");


//---------> Validator
    Flight::map('Validate', function(){
        if(!\App\Middleware\Auth::VerifyAuthenication()){
            App\Config\Config::DefineError('#-401', 'Authentication is required for this application');
        }
    });