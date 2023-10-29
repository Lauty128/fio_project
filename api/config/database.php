<?php
    namespace Config;

    use PDO, PDOException;

    class Database{
            
        public static PDO | null $connection = null;

        private static string $server = DB_SERVER;
        private static string $dbname = DB_NAME;
        private static string $user = DB_USER;
        private static string $password = DB_PASSWORD;
        
        static function connect(){
            if(self::$connection instanceof PDO) return;
            try{
                # configurar  la conexion con los datos de la clase
                $conection = "mysql:host=".self::$server.";dbname=".self::$dbname;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false, 
                ];
                
                # Ejecutar conexion
                $PDO = new PDO($conection, self::$user, self::$password, $options);
                self::$connection = $PDO;
            }
            # En caso de un error se devuelve un PDOEXception
            catch(PDOException $error){
                # Se crea un objeto con los  siguientes atributos
                DefineError('#-000', $error->getMessage());
            }
        }
    }