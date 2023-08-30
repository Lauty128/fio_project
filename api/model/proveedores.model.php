<?php 
    #--- DB connection
    require 'config/database.php';
    $database = new Database();
    $PDO = $database->connect();

    class ProveedoresModel{

        static function getAll($offset, $limit)
        {
            # llamamos a la variable global $PDO
            global $PDO;
            
            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                # Creamos la query con los parametros recibidos
                $sql = 'SELECT * FROM proveedor LIMIT :limit OFFSET :offset';

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
        }

    }