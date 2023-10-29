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

        static function getTotal(null | array $options):int | array
        {
            
            $sql = 'SELECT COUNT(e.equipmentID) as total FROM equipment e';
                # Crear variaciones en base a las opciones

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
            # Creamos la query con los parametros recibidos
            $sql = 'SELECT e.equipmentID, e.name, e.categoryID, c.name as category 
                FROM equipment e
                JOIN category c ON e.categoryID = c.categoryID';
            
            # Crear variaciones en base a las opciones
            if($options != null){
                $sql .= ' '.getWhere($options);
            }

            # Ordenar con los datos recibidos
            $sql .= ' '.defineOrder($order, 'equipment');
            
            # Agregar la paginaciÃ³n
            $sql .= ' LIMIT :limit OFFSET :offset';
            
            #-------------------- PREPARAR Y EJECUTAR CONSULTA
            try{
                # Preparamos la query con el string generado
                $query = Database::$connection->prepare($sql);
    
                # Definimos los valores de los parametros
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                # Retornamos el valor para usarlo en proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

        static function getProviders(int $id, int $offset = 0, int $limit = 20)
        {
            
            # Creamos la query con los parametros recibidos
            $sql="SELECT p.providerID, p.name 
                    FROM provider_equipment pe
                    JOIN provider p ON pe.providerID = p.providerID
                    WHERE pe.equipmentID = :id
                    LIMIT :limit
                    OFFSET :offset ";
            try{
                # Preparamos la query con el string generado
                $query = Database::$connection->prepare($sql);

                # Definimos los valores de los parametros
                $query->bindParam(':id', $id);
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # Ejecutamos  la consulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # Retornamos el valor para usarlo en proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

        static function getOne(int $id)
        { 
            
        
            # Creamos la query con los parametros recibidos
            $sql="SELECT e.*, c.name as category
                FROM equipment e 
                JOIN category c ON e.categoryID = c.categoryID 
                WHERE equipmentID = :id";
            
            try{
                # Preparamos la query con el string generado
                $query = Database::$connection->prepare($sql);

                # Definimos los valores de los parametros
                $query->bindParam(':id', $id);
                
                # Ejecutamos  la cnsulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetch(PDO::FETCH_ASSOC);
                
                # Retornamos el valor para usarlo en proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

    }