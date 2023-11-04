<?php
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    
    #----- BD CONNECTION
    use Config\Database;
    Database::connect();

    #--------------------------------------------------------------------

    class EquipmentsModel{ 

        static function getTotalByProviders(string $id):int | array
        {
            $sql = 'SELECT COUNT(pe.equipmentID) as total FROM equipment e
            JOIN provider_equipment pe ON e.equipmentID = pe.equipmentID
            WHERE pe.equipmentID = :id';
            
            try{
                # Prepare query
                $query = Database::$connection->prepare($sql);
                
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
                DefineError('#-001', $error->getMessage());
            }
        }

        //Function to get total cuantity of equipments
        static function getTotal(null | array $options):int | array
        {
            $sql = 'SELECT COUNT(e.equipmentID) as total FROM equipment e';

            # Create variations based on the options
            if(isset($options['category'])){
                $sql .= ' JOIN category c ON e.categoryID = c.categoryID';
            }

            if($options != null){
                $sql .= ' '.getWhere($options);
            }

            try{
                # Create query and execute
                $query = Database::$connection->prepare($sql);

                $query->execute();

                # Get the response of the 'total' field
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # return response
                return $response;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

        
        static function getAll($offset, $limit, $order, array | null $options)
        {
            
            #------------------- CREAR QUERY
            # Create the query with the received parameters
            $sql = 'SELECT e.equipmentID, e.name, e.categoryID, c.name as category 
                FROM equipment e
                JOIN category c ON e.categoryID = c.categoryID';
            
            # Create variations based on the options
            if($options != null){
                $sql .= ' '.getWhere($options);
            }

            # Sort with the received data
            $sql .= ' '.defineOrder($order, 'equipment');
            
            # Add pagination
            $sql .= ' LIMIT :limit OFFSET :offset';
            
            #-------------------- PREPARAR Y EJECUTAR CONSULTA
            try{
                # Prepare the query with the generated string
                $query = Database::$connection->prepare($sql);
    
                # Define the values of the parameters
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # Execute the query
                $query->execute();
                # Get an array with the received data
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                # Return the value to use it in proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

        static function getProviders(int $id, int $offset = 0, int $limit = 20)
        {
            
            # Create the query with the received parameters
            $sql="SELECT p.providerID, p.name 
                    FROM provider_equipment pe
                    JOIN provider p ON pe.providerID = p.providerID
                    WHERE pe.equipmentID = :id
                    LIMIT :limit
                    OFFSET :offset ";
            try{
                # Prepare the query with the generated string
                $query = Database::$connection->prepare($sql);

                # Define the parameter values
                $query->bindParam(':id', $id);
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # Execute the query
                $query->execute();
                # Obtain an array with the received data
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # Return the value to use it in proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

        static function getOne(int $id)
        { 
            # Create the query with the received parameters
            $sql="SELECT e.*, c.name as category
                FROM equipment e 
                JOIN category c ON e.categoryID = c.categoryID 
                WHERE equipmentID = :id";
            
            try{
                
                # Prepare the query with the generated string
                $query = Database::$connection->prepare($sql);

                # Define the parameter values
                $query->bindParam(':id', $id);
                
                # Execute the query
                $query->execute();
                # Obtain an array with the received data
                $data = $query->fetch(PDO::FETCH_ASSOC);
                
                # Return the value to use it in proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

    }