<?php
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    # Importar variable PDO del archivo global (index.php)
    global $PDO;

    #----- BD CONNECTION
    require 'config/database.php';
    $database = new Database();
    $PDO = $database->connect();
    #--------------------------------------------------------------------

    class EquipmentsModel{ 
        
        static function getAll($offset, $limit, $order, array | null $options)
        {
            # llamamos a la variable global $PDO
            global $PDO;
            
            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                #------------------- CREAR QUERY
                    # Creamos la query con los parametros recibidos
                    $sql = 'SELECT e.cod_equipo, e.nombre, e.cod_categoria, c.nombre as categoria 
                        FROM equipo e
                        JOIN categoria c ON e.cod_categoria = c.cod_categoria';
                    
                    # Crear variaciones en base a las opciones
                    if($options != null){
                        $sql .= ' '.getWhere($options);
                    }

                    # Ordenar con los datos recibidos
                    $sql .= ' '.defineOrder($order);
                    
                    # Agregar la paginación
                    $sql .= ' LIMIT :limit OFFSET :offset';
                
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
                $sql="SELECT p.cod_proveedor, p.nombre 
                        FROM proveedor_equipo pe
                        JOIN proveedor p ON pe.cod_proveedor = p.cod_proveedor
                        WHERE pe.cod_equipo = :id
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
                    FROM equipo 
                    WHERE cod_equipo = :id";

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
