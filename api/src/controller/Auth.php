<?php 

    namespace App\Controller;

    # Importar codigo
    use Flight;

    class Auth{

        static function login()
        {
            $body = Flight::request();
            Flight::json($body);
            exit();
        }
    }