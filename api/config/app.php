<?php
    # Import the errors of the errors.php file 
    require_once 'config/errors.php';

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


//---------> Define timezone
    date_default_timezone_set("America/Argentina/Buenos_Aires");

//----- Access to system
    #define('ALLOWED_HOSTS', ['proyecto-fio.local','lautarosilverii.000webhostapp.com']);
    define('ACCES_TOKEN', '<token>');

//----- Connect with database
    define('DB_SERVER','localhost');
    define('DB_NAME','fio_project');
    define('DB_USER','root');
    define('DB_PASSWORD','');

//------ DEFAULT VALUES
    define('PAGE', 0);
    define('LIMIT', 40);
    define('SMALL_LIMIT', 10);
    define('ORDER', 'default');

//------ Set of data
    define('ORDER_TYPES', ['N-ASC','N-DESC','ID-ASC','ID-DESC']);

//------ FUNCTIONS
    
    //ERROR HANDLER
    function DefineError($code, $errorMessage = 'Error'){
        // Define response to return
        $response = [
            ...ERROR_HANDLER[$code],
            'error_message' => $errorMessage
        ] ?? false;

        Flight::json($response, $response['http_code']);
        exit();
    }