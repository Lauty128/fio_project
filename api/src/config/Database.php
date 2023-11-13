<?php

    namespace App\Config;

    use PDO, PDOException;
    use App\Config\Config;

    class Database{
        
         # Viariable used to define the database connection
        public static PDO | null $connection = null;

        # Variable used to set up data base connection
        private static string $server = Config::DB_SERVER;
        private static string $dbname = Config::DB_NAME;
        private static string $user = Config::DB_USER;
        private static string $password = Config::DB_PASSWORD;
        
        # Funtion to connect to the data base
        static function connect():void
        {
            # config the connection with the dates of the class
            # charset is used for forcing the type of charset. In this case utf8mb4
            if(self::$connection != null) return;
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
                Config::DefineError('#-000', $error->getMessage());
            }
        }

        #--------- VARIABLES PARA CONFIGURAR LOS BACKUPS
        const DROP_TABLES = "#USE fio_project;\n#-------------------- DROPS\nDROP TABLE IF EXISTS provider_equipment;\nDROP TABLE IF EXISTS equipment;\nDROP TABLE IF EXISTS category;\nDROP TABLE IF EXISTS provider;";

        const PROVIDERS_STRUCTURE = "CREATE TABLE `provider` (
          `providerID` int(10) NOT NULL,
          `name` varchar(100) NOT NULL,
          `web` varchar(100) DEFAULT NULL,
          `mail` varchar(100) DEFAULT NULL,
          `phone` varchar(80) DEFAULT NULL,
          `address` varchar(150) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        const EQUIPMENTS_STRUCTURE = "CREATE TABLE `equipment` (
          `equipmentID` int(10) NOT NULL,
          `name` varchar(100) NOT NULL,
          `categoryID` int(10) NOT NULL,
          `umdns` varchar(20) DEFAULT NULL,
          `description` TEXT DEFAULT NULL,
          `price` int(10) DEFAULT NULL,
          `specifications` varchar(150) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        const CATEGORIES_STRUCTURE = "CREATE TABLE `category` (
          `categoryID` int(10) NOT NULL,
          `name` varchar(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        const PROVIDER_EQUIPMENT_STRUCTURE = "CREATE TABLE `provider_equipment` (
          `providerID` int(10) NOT NULL,
          `equipmentID` int(10) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
      
        const RELATIONS_AND_CONFIGS = "
        --
        -- Indices de la tabla `category`
        --
        ALTER TABLE `category`
          ADD PRIMARY KEY (`categoryID`);
        
        --
        -- Indices de la tabla `equipment`
        --
        ALTER TABLE `equipment`
          ADD PRIMARY KEY (`equipmentID`),
          ADD KEY `categoryID` (`categoryID`);
        
        --
        -- Indices de la tabla `provider`
        --
        ALTER TABLE `provider`
          ADD PRIMARY KEY (`providerID`);
        
        --
        -- Indices de la tabla `provider_equipment`
        --
        ALTER TABLE `provider_equipment`
          ADD PRIMARY KEY (`providerID`,`equipmentID`),
          ADD KEY `equipmentID` (`equipmentID`);
          
        --
        -- AUTO_INCREMENT de la tabla `category`
        --
          ALTER TABLE `category`
            MODIFY `categoryID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
          
        --
        -- AUTO_INCREMENT de la tabla `equipment`
        --
          ALTER TABLE `equipment`
            MODIFY `equipmentID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=598;
          
        --
        -- AUTO_INCREMENT de la tabla `provider`
        --
          ALTER TABLE `provider`
            MODIFY `providerID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;
            
            ALTER TABLE `equipment`
          ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`categoryID`) REFERENCES `category` (`categoryID`);

        --
        -- Filtros para la tabla `provider_equipment`
        --
          ALTER TABLE `provider_equipment`
          ADD CONSTRAINT `provider_equipment_ibfk_1` FOREIGN KEY (`providerID`) REFERENCES `provider` (`providerID`),
          ADD CONSTRAINT `provider_equipment_ibfk_2` FOREIGN KEY (`equipmentID`) REFERENCES `equipment` (`equipmentID`);";
    }