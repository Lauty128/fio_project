<?php 
    
    namespace App\Model;

    use App\Config;

    use PDO, PDOException;

    #----- DataBase Connection
    Config\Database::connect();

    class Categories{ 

        static function getAll()
        {
            # Crear Consulta SQL
            $sql = 'SELECT * FROM category';
            
            try{
                # Se prepara y ejecuta la consulta
                $query = Config\Database::$connection->query($sql);
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # El valor es retornado 
                // @data > Array | false
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

        static function getAllByProvider($id)
        {
            # Creamos la consulta con los parámetros recibidos
            $sql = 'SELECT DISTINCT c.categoryID,c.name FROM provider p
            JOIN provider_equipment pe ON p.providerID = pe.providerID
            JOIN equipment e ON e.equipmentID = pe.equipmentID
            JOIN category c ON e.categoryID = c.categoryID
            WHERE pe.providerID = :id';
            
            try{
                # Creamos la consulta con la cadena generada
                $query = Config\Database::$connection->prepare($sql);

                # Definimos los valores de los parámetros
                $query->bindParam(':id', $id);

                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un array con los datos obtenidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # Devolvemos el valor para usarlo en providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

    }