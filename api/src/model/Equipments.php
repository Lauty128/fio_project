<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    namespace App\Model;
    
    #----- Importar Codigo
    use App\Util;
    use PDO, PDOException;
    use App\Config;
use Flight;

    #----- Conexion de la base de Datos
    Config\Database::connect();

    #--------------------------------------------------------------------
    
    class Equipments{ 
        
        static function getAll($offset, $limit, $order, array | null $options)
        {
            
        #------------------- CREAR CONSULTA
            # Creamos la consulta con los parametros recibidos
            $sql = 'SELECT e.id, e.name, e.category_id, c.name as category 
                FROM equipments e
                JOIN categories c ON e.category_id = c.id';
            
            # Creamos variaciones basadas en la opciones
            if($options != null){
                //$sql .= ' '.Util\Queries::getWhere($options);
            }

            # Ordenar con los datos
            $sql .= ' '.Util\Queries::defineOrder($order, 'equipments');
            
            # Agragar paginacion
            //$sql .= ' ORDER BY e.equipmentID';
            $sql .= ' LIMIT :limit OFFSET :offset';

            // Flight::json($sql);
            // exit();
        #-------------------- PREPARAR Y EJECUTAR CONSULTA
            try{
                # Preparamos la consulta con la cadena generada
                $query = Config\Database::$connection->prepare($sql);
    
                # Definimos los valores de los parametros
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                # Devolver el valor para usarlo en providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

        static function getProviders(int $id, int $offset = 0, int $limit = 20)
        {
            
            # Creamos la consulta con los parámetros recibidos
            $sql="SELECT p.providerID, p.name 
                    FROM provider_equipment pe
                    JOIN provider p ON pe.providerID = p.providerID
                    WHERE pe.equipmentID = :id
                    LIMIT :limit
                    OFFSET :offset ";
            try{
                # Preparamos la consulta con la cadena generada
                $query = Config\Database::$connection->prepare($sql);

                # Definimos los valores de los parámetros
                $query->bindParam(':id', $id);
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # Devolvemos el valor para usarlo en providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

        static function getOne(int $id)
        { 
            # reamos la consulta con los parámetros recibidos
            $sql="SELECT e.*, c.name as category
                FROM equipment e 
                JOIN category c ON e.categoryID = c.categoryID 
                WHERE equipmentID = :id";
            
            try{
                # Preparamos la consulta con la cadena generada
                $query = Config\Database::$connection->prepare($sql);

                # Definimos los valores de los parámetros
                $query->bindParam(':id', $id);
                
                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetch(PDO::FETCH_ASSOC);
                
                # Devolvemos el valor para usarlo en providers.model.php
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
                # Preparamos la consulta
                $query = Config\Database::$connection->prepare($sql);
                
                # Se bindean los parametros de la consulta
                $query->bindParam(':id', $id);

                # Ejecutamos la consulta
                $query->execute();

                # Obtenemos la respuesta del campo 'total'
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # Devolver respuesta
                return $response;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

        static function getTotal(null | array $options):int | array
        {
            
            $sql = 'SELECT COUNT(e.id) as total FROM equipments e';
                # Crear variaciones basadas en las opciones
            if(isset($options['category'])){
                $sql .= ' JOIN categories c ON e.category_id = c.id';
            }

            if($options != null){
                $sql .= ' '.Util\Queries::getWhere($options);
            }

            try{
                # Crear consulta y ejecutar
                $query = Config\Database::$connection->prepare($sql);

                $query->execute();

                # Obtener la respuesta del campo 'total'
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # Devolver respuesta
                return $response;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            }
        }

    }