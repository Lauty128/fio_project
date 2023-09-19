<?php
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    # Importar variable PDO del archivo global (index.php)
    global $PDO;

    #----- BD CONNECTION
    if(!$PDO){
        require 'config/database.php';
        $database = new Database();
        $PDO = $database->connect();
    }
    #--------------------------------------------------------------------

    class EquipmentsModel{ 

        static function getTotalByProviders(string $id):int | array
        {
            # Call to the global variable $PDO
            global $PDO;

            # if $PDO is of type PDO, the following code will be executed
            if($PDO instanceof PDO){

                $sql = 'SELECT COUNT(pe.equipmentID) as total FROM equipment e
                JOIN provider_equipment pe ON e.equipmentID = pe.equipmentID
                WHERE pe.equipmentID = :id';

                # Prepare query
                $query = $PDO->prepare($sql);
                
                # Bind parameters
                $query->bindParam(':id', $id);

                # Execute query
                $query->execute();

                # Get the response of the 'total' field
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # return response
                return $response;
            }
            # if $PDO is of type PDOException, the following code will be executed
            else if($PDO instanceof PDOException){
                return [
                    'Error'=>500,
                    'Message'=>'Ocurrio un error al conectarse a la base de datos',
                    'Error-Message' => $PDO->getMessage()
                ];
            }
        }

        static function getTotal(null | array $options):int | array
        {
            # Call to the global variable $PDO
            global $PDO;

            # if $PDO is of type PDO, the following code will be executed
            if($PDO instanceof PDO){

                $sql = 'SELECT COUNT(e.equipmentID) as total FROM equipment e';
                 # Crear variaciones en base a las opciones

                if(isset($options['category'])){
                    $sql .= ' JOIN category c ON e.categoryID = c.categoryID';
                }

                if($options != null){
                    $sql .= ' '.getWhere($options);
                }
                //var_dump($sql); exit();
                # Create query and execute
                $query = $PDO->prepare($sql);

                $query->execute();


                # Get the response of the 'total' field
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # return response
                return $response;
            }
            # if $PDO is of type PDOException, the following code will be executed
            else if($PDO instanceof PDOException){
                return [
                    'Error'=>500,
                    'Message'=>'Ocurrio un error al conectarse a la base de datos',
                    'Error-Message' => $PDO->getMessage()
                ];
            }
        }

        
        static function getAll($offset, $limit, $order, array | null $options)
        {
            # llamamos a la variable global $PDO
            global $PDO;
            
            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
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
                    $sql .= ' '.defineOrder($order);
                    
                    # Agregar la paginación
                    $sql .= ' LIMIT :limit OFFSET :offset';
                    //var_dump($sql); exit();
                #-------------------- PREPARAR Y EJECUTAR CONSULTA
                    # Preparamos la query con el string generado
                    $query = $PDO->prepare($sql);

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
            else{
                return [
                    'Error'=>500,
                    'Message'=>'Ocurrio un error al conectarse a la base de datos',
                    'Error-Message' => $PDO->getMessage()
                ];
            }
        
        }

        static function getProviders(int $id, int $offset = 0, int $limit = 20)
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                # Creamos la query con los parametros recibidos
                $sql="SELECT p.providerID, p.name 
                        FROM provider_equipment pe
                        JOIN provider p ON pe.providerID = p.providerID
                        WHERE pe.equipmentID = :id
                        LIMIT :limit
                        OFFSET :offset ";

                # Preparamos la query con el string generado
                $query = $PDO->prepare($sql);

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
            else{
                return [
                    'Error'=>500,
                    'Message'=>'Ocurrio un error al conectarse a la base de datos',
                    'Error-Message' => $PDO->getMessage()
                ];
            }
        }

        static function getOne(int $id)
        { 
            # llamamos a la variable global $PDO de este archivo
            global $PDO;
            
            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException')
            {
            # Creamos la query con los parametros recibidos
            $sql="SELECT *
                    FROM equipment 
                    WHERE equipmentID = :id";

            # Preparamos la query con el string generado
            $query = $PDO->prepare($sql);

            # Definimos los valores de los parametros
            $query->bindParam(':id', $id);
            
            # Ejecutamos  la cnsulta
            $query->execute();
            # Obtenemos un array con los datos recibidos
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            
            # Retornamos el valor para usarlo en proveedores.model.php
            return $data;
        }
        else{
            return [
                'Error'=>500,
                'Message'=>'Ocurrio un error al conectarse a la base de datos',
                'Error-Message' => $PDO->getMessage()
            ];
        }
        }

    }
