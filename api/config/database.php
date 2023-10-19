<?php

    class Database{
        
        private string $server;
        private string $dbname;
        private string $user;
        private string $password;

        function __construct()
        {
            # Definir los valores de las propiedades de la clase con las constantes del archivo variables.php 
            $this->server = constant('DB_SERVER');
            $this->dbname = constant('DB_NAME');
            $this->user = constant('DB_USER');
            $this->password = constant('DB_PASSWORD');
        }
        
        function connect(){
            try{
                # configurar  la conexion con los datos de la clase
                $conection = "mysql:host=".$this->server.";dbname=".$this->dbname;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false, 
                ];
                
                # Ejecutar conexion
                $PDO = new PDO($conection, $this->user, $this->password, $options);
                return $PDO;
            }
            # En caso de un error se devuelve un PDOEXception
            catch(PDOException $error){
                # Se crea un objeto con los  siguientes atributos
                $response = DefineError('#-000', $error->getMessage());
                
                # Con flightPHP retornamos ese objeto en formato JSON
                Flight::json($response, $response['http_code']);
                # Cancelamos la ejecucion del resto del codigo, ya que al exitir un error, no es importante el resto
                exit();

                # Este script se ejecuta cada vez que se llama a un modelo.
                # Por lo tanto aqui manejamos los errores de conexion de toda la aplicacion.
            }
        
        }
    }