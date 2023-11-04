<?php
    namespace Config;

    use PDO, PDOException;

    class Database{
            
        # Viariable used to define the database connection
        public static PDO | null $connection = null;

        # Variable used to set up data base connection
        private static string $server = DB_SERVER;
        private static string $dbname = DB_NAME;
        private static string $user = DB_USER;
        private static string $password = DB_PASSWORD;
        
        # Funtion to connect to the data base
        static function connect(){
            if(self::$connection instanceof PDO) return;
            try{
                # config the connection with the dates of the class
                # charset is used for forcing the type of charset. In this case utf8mb4
                $conection = "mysql:host=".self::$server.";dbname=".self::$dbname.";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false, 
                ];
                
                # Execute connection
                $PDO = new PDO($conection, self::$user, self::$password, $options);
                 # Assing to $connection the value of the object PDO
                self::$connection = $PDO;
            }
            # In case of an error return a PDOExeption
            catch(PDOException $error){
                # Return a message error with the detail of the cod #-000 and the getMessage()
                DefineError('#-000', $error->getMessage());
            }
        }
    }