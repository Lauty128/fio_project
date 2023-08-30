<?php 
    # Importar variables
    require_once 'config/variables.php';

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
            catch(PDOException $error){
                # En caso de un error se devuelve un PDOEXception
                return $error;
            }
        
        }
    }