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
    
    class Equipments{ 
        
        static function getAll($offset, $limit, $order, array | null $options)
        {
            
        #------------------- CREATE QUERY
            # We create the query with the received parameters
            $sql = 'SELECT e.equipmentID, e.name, e.categoryID, c.name as category 
                FROM equipment e
                JOIN category c ON e.categoryID = c.categoryID';
            
            # Create variations based on the options
            if($options != null){
                $sql .= ' '.Util\Queries::getWhere($options);
            }

            # Sort with the received data
            $sql .= ' '.Util\Queries::defineOrder($order, 'equipment');
            
            # Add pagination
            $sql .= ' LIMIT :limit OFFSET :offset';
            
        #-------------------- PREPARE AND EXECUTE QUERY
            try{
                # We prepare the query with the generated string
                $query = Config\Database::$connection->prepare($sql);
    
                # We define the values of the parameters
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # We execute the query
                $query->execute();
                # We obtain an array with the received data
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                # We return the value to use it in providers.model.php.
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

        static function getProviders(int $id, int $offset = 0, int $limit = 20)
        {
            
            # We create the query with the received parameters.
            $sql="SELECT p.providerID, p.name 
                    FROM provider_equipment pe
                    JOIN provider p ON pe.providerID = p.providerID
                    WHERE pe.equipmentID = :id
                    LIMIT :limit
                    OFFSET :offset ";
            try{
                # We prepare the query with the generated string.
                $query = Config\Database::$connection->prepare($sql);

                # We define the parameter values.
                $query->bindParam(':id', $id);
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # We execute the query
                $query->execute();
                # We obtain an array with the received data
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # We return the value to use it in providers.model.php.
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

        static function getOne(int $id)
        { 
            # We create the query with the received parameters
            $sql="SELECT e.*, c.name as category
                FROM equipment e 
                JOIN category c ON e.categoryID = c.categoryID 
                WHERE equipmentID = :id";
            
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
        
        static function getTotalByProviders(string $id):int | array
        {
            
            $sql = 'SELECT COUNT(pe.equipmentID) as total FROM equipment e
            JOIN provider_equipment pe ON e.equipmentID = pe.equipmentID
            WHERE pe.equipmentID = :id';
            
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

        static function getTotal(null | array $options):int | array
        {
            
            $sql = 'SELECT COUNT(e.equipmentID) as total FROM equipment e';
                # Create variations based on the options

            if(isset($options['category'])){
                $sql .= ' JOIN category c ON e.categoryID = c.categoryID';
            }

            if($options != null){
                $sql .= ' '.Util\Queries::getWhere($options);
            }

            try{
                # Create query and execute
                $query = Config\Database::$connection->prepare($sql);

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