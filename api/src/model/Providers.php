<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    namespace App\Model;
    
    #----- Import code
    use App\Util;
    use PDO, PDOException;
    use App\Config;
    
    #----- DataBase Connection
    Config\Database::connect();

    #--------------------------------------------------------------------
    
    class Providers{ 

        static function getAll($offset, $limit, string $order, array | null $options)
        {
           #------------------- CREAR QUERY
            # We create the query with the received parameters
            $sql = (isset($options['equipments']))
                # If the equipment filter exists, we add one more field to display and a JOIN to the 'provider_equipment' table
                ? 'SELECT DISTINCT p.*, count(pe.providerID) as equipment
                    FROM provider p
                    JOIN provider_equipment pe ON pe.providerID = p.providerID'
                # If it doesn't exist, we perform a simple SELECT
                :  'SELECT DISTINCT p.* FROM provider p';

            # If the category and equipment filters exist, we only add a JOIN to the 'equipment' table
            if(isset($options['category']) && isset($options['equipments'])){
                $sql .= " JOIN equipment e ON e.equipmentID = pe.equipmentID";
            }
            # If the equipment filter doesn't exist, but the category filter does, we perform a JOIN on both tables
            elseif(isset($options['category'])){
                $sql .= " INNER JOIN provider_equipment pe ON pe.providerID = p.providerID
                INNER JOIN equipment e ON e.equipmentID = pe.equipmentID";
            }

            # Create variations based on the options
            if($options != null){
                $sql .= ' '.Util\Queries::getWhere($options);
            }
            
            # Sort with the received data
            $sql .= ' '.Util\Queries::defineOrder($order, 'provider');

            # If we want to obtain the total of equipment sold, we must group the results
            if(isset($options['equipments'])){
                $sql .= ' GROUP BY p.providerID';
            }

            # Add pagination
            $sql .= ' LIMIT :limit OFFSET :offset';

            #-------------------- PREPARAR Y EJECUTAR CONSULTA
            try{
                # We prepare the query with the generated string
                $query = Config\Database::$connection->prepare($sql);

                # We define the parameter values
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);

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

        static function getEquipments(int $id, int $offset = 0, int $limit = 20){
            # We create the query with the received parameters
            $sql="SELECT e.equipmentID, e.name, e.categoryID,c.name as category
                    FROM provider_equipment pe
                    JOIN equipment e ON pe.equipmentID = e.equipmentID
                    JOIN category c ON e.categoryID = c.categoryID
                    WHERE pe.providerID = :id
                    LIMIT :limit
                    OFFSET :offset ";
            try{
                $query = Config\Database::$connection->prepare($sql);
                
                # We define the parameter values
                $query->bindParam(':id', $id);
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # We execute the query
                $query->execute();
                # We obtain an array with the received data.
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                # We return the value to use it in providers.model.php
                # We prepare the query with the generated string
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }

        static function getOne(int $id): Array | bool
        {
            # We create the query with the received parameters
            $sql="SELECT * FROM provider WHERE providerID = :id";

            try{
                # We prepare the query with the generated string
                $query = Config\Database::$connection->prepare($sql);

                # We define the parameter values
                $query->bindParam(':id', $id);
                
                # We execute the query
                $query->execute();
                # We obtain an array with the received data
                $data = $query->fetch(PDO::FETCH_ASSOC);

                # We return the value to use it in providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }

        static function getTotal(array | null $options):int | array
        {
            # Create query
            $sql = 'SELECT COUNT(DISTINCT p.providerID) as total FROM provider p';
            
            if(isset($options['category'])){
                $sql .= ' INNER JOIN provider_equipment pe ON pe.providerID = p.providerID
                INNER JOIN equipment e ON pe.equipmentID = e.equipmentID';
            }

            if($options != null){
                $sql .= ' '.Util\Queries::getWhere($options);
            }
            
            try{
                # Execute query
                $query = Config\Database::$connection->query($sql);
                
                # Get the response of the 'total' field
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # return response
                return $response;
            }   
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }

        static function getTotalByEquipments(string $id):int | array
        {# Create query
            $sql = 'SELECT COUNT(pe.providerID) as total FROM provider p
            JOIN provider_equipment pe ON p.providerID = pe.providerID
            WHERE pe.providerID = :id';

             try{
                # Prepare query
                $query = Config\Database::$connection->prepare($sql);
                
                # Bind parameters
                $query->bindParam(':id', $id);

                # Execute query
                $query->execute();

                # Get the response of the 'total' field
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # return response
                return $response;
            }   
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }
    }