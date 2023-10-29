<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP

    #----- BD CONNECTION
    use Config\Database;
    Database::connect();
    
    #--------------------------------------------------------------------
    
    class ProvidersModel{ 

        static function getTotalByEquipments(string $id):int | array
        {# Create query
            $sql = 'SELECT COUNT(pe.providerID) as total FROM provider p
            JOIN provider_equipment pe ON p.providerID = pe.providerID
            WHERE pe.providerID = :id';

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

        static function getTotal(array | null $options):int | array
        {
            # Create query
            $sql = 'SELECT COUNT(DISTINCT p.providerID) as total FROM provider p';
            
            if(isset($options['category'])){
                $sql .= ' INNER JOIN provider_equipment pe ON pe.providerID = p.providerID
                INNER JOIN equipment e ON pe.equipmentID = e.equipmentID';
            }

            if($options != null){
                $sql .= ' '.getWhere($options);
            }
            
            try{
                # Execute query
                $query = Database::$connection->query($sql);
                
                # Get the response of the 'total' field
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # return response
                return $response;
            }   
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            } 
        }

        static function getAll($offset, $limit, string $order, array | null $options)
        {
        #------------------- CREAR QUERY
            # Creamos la query con los parametros recibidos
            $sql = (isset($options['equipments']))
                # Si existe el filtro de equipos agregamos un campo mas a mostrar y un JOIN a la tabla proveedor_equipo
                ? 'SELECT DISTINCT p.*, count(pe.providerID) as equipment
                    FROM provider p
                    JOIN provider_equipment pe ON pe.providerID = p.providerID'
                # Si no existe hacemos un SELECT simple
                :  'SELECT DISTINCT p.* FROM provider p';

            # SI existe el filtro de categoria y equipos solo agregamos el JOIN a la tabla equipo
            if(isset($options['category']) && isset($options['equipments'])){
                $sql .= " JOIN equipment e ON e.equipmentID = pe.equipmentID";
            }
            # Si no existe el filtro de equipos pero si el de categoria hcemos el JOIN a ambas tablas
            elseif(isset($options['category'])){
                $sql .= " INNER JOIN provider_equipment pe ON pe.providerID = p.providerID
                INNER JOIN equipment e ON e.equipmentID = pe.equipmentID";
            }

            # Crear variaciones en base a las opciones
            if($options != null){
                $sql .= ' '.getWhere($options);
            }
            
            # Ordenar con los datos recibidos
            $sql .= ' '.defineOrder($order, 'provider');

            # Si queremos obtener el total de equipamientos vendido debemos agrupar los resultados
            if(isset($options['equipments'])){
                $sql .= ' GROUP BY p.providerID';
            }

            # Agregar la paginaciÃ³n
            $sql .= ' LIMIT :limit OFFSET :offset';

        #-------------------- PREPARAR Y EJECUTAR CONSULTA
        try{
            # Preparamos la query con el string generado
            $query = Database::$connection->prepare($sql);

            # Definimos los valores de los parametros
            $query->bindParam(':limit', $limit);
            $query->bindParam(':offset', $offset);

            # Ejecutamos  la cnsulta
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
    
        static function getEquipments(int $id, int $offset = 0, int $limit = 20){
            # Creamos la query con los parametros recibidos
            $sql="SELECT e.equipmentID, e.name, e.categoryID,c.name as category
                    FROM provider_equipment pe
                    JOIN equipment e ON pe.equipmentID = e.equipmentID
                    JOIN category c ON e.categoryID = c.categoryID
                    WHERE pe.providerID = :id
                    LIMIT :limit
                    OFFSET :offset ";
            try{
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
                # Preparamos la query con el string generado
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            } 
        }

        static function getOne(int $id): Array | bool
        {
            # Creamos la query con los parametros recibidos
            $sql="SELECT * FROM provider WHERE providerID = :id";

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