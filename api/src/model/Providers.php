<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    namespace App\Model;
    
    #----- Importar código
    use App\Util;
    use PDO, PDOException;
    use App\Config;
    
    #----- Conexión a la base de datos
    Config\Database::connect();

    #--------------------------------------------------------------------
    
    class Providers{ 

        static function getAll($offset, $limit, string $order, array | null $options)
        {
           #------------------- CREAR QUERY
            # Creamos la consulta con los parámetros recibidos
            $sql = (isset($options['equipments']))
                # Si el filtro de equipo existe, agregamos un campo más para mostrar y realizamos un JOIN con la tabla 'provider_equipment'
                ? 'SELECT DISTINCT p.*, count(pe.providerID) as equipment
                    FROM provider p
                    JOIN provider_equipment pe ON pe.providerID = p.providerID'
                # Si no existe, realizamos un SELECT simple
                :  'SELECT DISTINCT p.* FROM provider p';

            # Si existen los filtros de categoría y equipo, solo agregamos un JOIN a la tabla 'equipment'
            if(isset($options['category']) && isset($options['equipments'])){
                $sql .= " JOIN equipment e ON e.equipmentID = pe.equipmentID";
            }
            # Si el filtro de equipo no existe pero el filtro de categoría sí, realizamos un JOIN en ambas tablas
            elseif(isset($options['category'])){
                $sql .= " INNER JOIN provider_equipment pe ON pe.providerID = p.providerID
                INNER JOIN equipment e ON e.equipmentID = pe.equipmentID";
            }

            # Crear variaciones basadas en las opciones
            if($options != null){
                $sql .= ' '.Util\Queries::getWhere($options);
            }
            
            # Ordenar con los datos recibidos
            $sql .= ' '.Util\Queries::defineOrder($order, 'provider');

            # Si queremos obtener el total de equipos vendidos, debemos agrupar los resultados
            if(isset($options['equipments'])){
                $sql .= ' GROUP BY p.providerID';
            }

            # Agregar paginación
            $sql .= ' LIMIT :limit OFFSET :offset';

            #-------------------- PREPARAR Y EJECUTAR CONSULTA
            try{
                # Preparamos la consulta con la cadena generada
                $query = Config\Database::$connection->prepare($sql);

                # Definimos los valores de los parámetros
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);

                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un arreglo con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # Retornamos el valor para usarlo en providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        
        }

        static function getEquipments(int $id, int $offset = 0, int $limit = 20){
            # Creamos la consulta con los parámetros recibidos
            $sql="SELECT e.equipmentID, e.name, e.categoryID,c.name as category
                    FROM provider_equipment pe
                    JOIN equipment e ON pe.equipmentID = e.equipmentID
                    JOIN category c ON e.categoryID = c.categoryID
                    WHERE pe.providerID = :id
                    LIMIT :limit
                    OFFSET :offset ";
            try{
                $query = Config\Database::$connection->prepare($sql);
                
                # Definimos los valores de los parámetros
                $query->bindParam(':id', $id);
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                # Retornamos el valor para usarlo en providers.model.php
                
                # Preparamos la consulta con la cadena generada
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }

        static function getOne(int $id): Array | bool
        {
            # Creamos la consulta con los parámetros recibidos
            $sql="SELECT * FROM provider WHERE providerID = :id";

            try{
                # Preparamos la consulta con la cadena generada
                $query = Config\Database::$connection->prepare($sql);

                # Definimos los valores de los parámetros
                $query->bindParam(':id', $id);
                
                # Ejecutamos la consulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetch(PDO::FETCH_ASSOC);

                # Retornamos el valor para usarlo en providers.model.php
                return $data;
            }
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }

        static function getTotal(array | null $options):int | array
        {
            # Creamos la consulta
            $sql = 'SELECT COUNT(DISTINCT p.providerID) as total FROM provider p';
            
            if(isset($options['category'])){
                $sql .= ' INNER JOIN provider_equipment pe ON pe.providerID = p.providerID
                INNER JOIN equipment e ON pe.equipmentID = e.equipmentID';
            }

            if($options != null){
                $sql .= ' '.Util\Queries::getWhere($options);
            }
            
            try{
                # Executamos la consulta
                $query = Config\Database::$connection->query($sql);
                
                # Obtenemos la respuesta del campo 'total'
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # devolver respuesta
                return $response;
            }   
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }

        static function getTotalByEquipments(string $id):int | array
        {# Creamos la consulta
            $sql = 'SELECT COUNT(pe.providerID) as total FROM provider p
            JOIN provider_equipment pe ON p.providerID = pe.providerID
            WHERE pe.providerID = :id';

             try{
                # Preparar consulta
                $query = Config\Database::$connection->prepare($sql);
                
                # Vincular parámetros
                $query->bindParam(':id', $id);

                # Ejecutar consulta
                $query->execute();

                # Obtener la respuesta del campo 'total'
                $response = $query->fetch(PDO::FETCH_ASSOC)['total'];

                # devolver respuesta
                return $response;
            }   
            catch(PDOException $error){
                Config\Config::DefineError('#-001', $error->getMessage());
            } 
        }
    }