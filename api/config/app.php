<?php

# ----------- CONFIGURAR CORS
    // Specify domains from which requests are allowed
    header('Access-Control-Allow-Origin: *');
    // Specify which request methods are allowed
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    // Additional headers which may be sent along with the CORS request
    header('Access-Control-Allow-Headers: X-Requested-With,Authorization,Content-Type');

//----- Definir timezone
    date_default_timezone_set("America/Argentina/Buenos_Aires");

//----- Conexión con base de datos
    define('DB_SERVER','localhost');
    define('DB_NAME','fio_project');
    define('DB_USER','root');
    define('DB_PASSWORD','');

//------ DEFAULT VALUES
    #Aqui definimos los valores por defecto que tomaremos en el codigo
    define('PAGE', 0);
    define('LIMIT', 40);
    define('SMALL_LIMIT', 10);
    define('ORDER', 'default');

//------ CONJUNTO DE DATOS
    define('ORDER_TYPES', ['N-ASC','N-DESC','default']);

//------ FUNCIONES
    function queryErrorHandler(PDOException $error){
        # Esta funcion maneja los errores de queries, por lo que modificando esto, se cambia el tipo de respuesta de error de una consulta.
        $response = [
            'Error'=>500,
            'Message'=>'Ocurrio un error durante la consulta',
            'Error-Message' => $error->getMessage()
        ];

        # El valor que devuelve esta funcion debe ser retornado por el catch() que lo llama. 
        return $response;

        # Igual que en database.php podemos devolver esto directamente con FlightPHP, como se ve a continaucion:
        
        // Flight::json($response);
        // exit();
    }