<?php 

    namespace App\Controller;

    # Import code
    use Flight;

    class Auth{

        static function login()
        {
            $body = Flight::request();
            Flight::json($body);
            exit();
        }
    }