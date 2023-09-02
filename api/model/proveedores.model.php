<?php 
    #--- DB connection
    require 'config/database.php';
    $database = new Database();
    $PDO = $database->connect();

    class ProveedoresModel{ 

        static function getAll($offset, $limit, bool $equipments)
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                # Creamos la query con los parametros recibidos
                $sql = ($equipments)
                ?   'SELECT p.*, count(pe.cod_proveedor) as equipos
                    FROM proveedor p
                    JOIN proveedor_equipo pe ON p.cod_proveedor = pe.cod_proveedor
                    group by p.cod_proveedor'
                :   'SELECT * FROM proveedor';
                    //ORDER BY equipos DESC'
                
                # Agregar la paginación
                $sql .= ' LIMIT :limit OFFSET :offset';

                # Preparamos la query con el string generado
                $query = $PDO->prepare($sql);

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
            else{
                return [
                    'Error'=>500,
                    'Message'=>'Ocurrio un error al conectarse a la base de datos',
                    'Error-Message' => $PDO->getMessage()
                ];
            }
        }

        static function getEquipments(int $id, int $offset = 0, int $limit = 10)
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                # Creamos la query con los parametros recibidos
                $sql="SELECT e.nombre, c.nombre as categoria
                        FROM proveedor_equipo pe
                        JOIN equipo e ON pe.cod_equipo = e.cod_equipo
                        JOIN categoria c ON e.cod_categoria = c.cod_categoria
                        WHERE pe.cod_proveedor = :id
                        LIMIT :limit
                        OFFSET :offset ";

                # Preparamos la query con el string generado
                $query = $PDO->prepare($sql);

                # Definimos los valores de los parametros
                $query->bindParam(':id', $id);
                $query->bindParam(':limit', $limit);
                $query->bindParam(':offset', $offset);
                
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
        
        static function getOne(int $id)
        {
            # Use the PDO variable of the line 6
            global $PDO;
        
            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                # Creamos la query con los parametros recibidos
                $sql="SELECT * FROM proveedor WHERE cod_proveedor = :id";

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