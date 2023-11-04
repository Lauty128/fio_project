<?php 
    
    namespace App\Model;

    use App\Config;

    use PDO, PDOException;

    #----- DataBase Connection
    Config\Database::connect();

    class Categories{ 

        static function getAll()
        {
            # We create the query with the received parameters
            $sql = 'SELECT * FROM category';
            
            try{
                # We prepare the query with the generated string
                $query = Config\Database::$connection->query($sql);
                
                # We obtain an array with the received data
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # We return the value to use it in providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

        static function getAllByProvider($id)
        {
            # We create the query with the received parameters
            $sql = 'SELECT DISTINCT c.categoryID,c.name FROM provider p
            JOIN provider_equipment pe ON p.providerID = pe.providerID
            JOIN equipment e ON e.equipmentID = pe.equipmentID
            JOIN category c ON e.categoryID = c.categoryID
            WHERE pe.providerID = :id';
            
            try{
                # We prepare the query with the generated string
                $query = Config\Database::$connection->prepare($sql);

                # We define the parameter values
                $query->bindParam(':id', $id);

                # We execute the query
                $query->execute();
                # We obtain an array with the received data
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # We return the value to use it in providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

    }