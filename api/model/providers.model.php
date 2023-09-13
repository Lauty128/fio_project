<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    # Importar variable PDO del archivo global (index.php)
    global $PDO;

    #----- BD CONNECTION
    require 'config/database.php';
    $database = new Database();
    $PDO = $database->connect();
    #--------------------------------------------------------------------
    
    class ProvidersModel{ 

        static function getAll($offset, $limit, string $order, array | null $options)
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                #------------------- CREAR QUERY
                    # Creamos la query con los parametros recibidos
                    $sql = (isset($options['equipments']))
                        # Si existe el filtro de equipos agregamos un campo mas a mostrar y un JOIN a la tabla proveedor_equipo
                        ? 'SELECT p.*, count(pe.cod_proveedor) as equipos
                            FROM proveedor p
                            JOIN proveedor_equipo pe ON pe.cod_proveedor = p.cod_proveedor'
                        # Si no existe hacemos un SELECT simple
                        :  'SELECT p.* FROM proveedor p';

                    # SI existe el filtro de categoria y equipos solo agregamos el JOIN a la tabla equipo
                    if(isset($options['category']) && isset($options['equipments'])){
                        $sql .= " JOIN equipo e ON e.cod_equipo = pe.cod_equipo";
                    }
                    # Si no existe el filtro de equipos pero si el de categoria hcemos el JOIN a ambas tablas
                    elseif(isset($options['category'])){
                        $sql .= " JOIN proveedor_equipo pe ON pe.cod_proveedor = p.cod_proveedor
                            JOIN equipo e ON e.cod_equipo = pe.cod_equipo";
                    }

                    # Crear variaciones en base a las opciones
                    if($options != null){
                        $sql .= ' '.getWhere($options);
                    }

                    # Ordenar con los datos recibidos
                    $sql .= ' '.defineOrder($order);

                    # Si queremos obtener el total de equipamientos vendido debemos agrupar los resultados
                    if(isset($options['equipments'])){
                        $sql .= ' GROUP BY p.cod_proveedor';
                    }
                    
                    # Agregar la paginación
                    $sql .= ' LIMIT :limit OFFSET :offset';
                //var_dump($sql);  exit();    

                #-------------------- PREPARAR Y EJECUTAR CONSULTA
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

        static function getEquipments(int $id, int $offset = 0, int $limit = 20)
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