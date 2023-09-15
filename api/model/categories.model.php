<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    # Importar variable PDO del archivo global (index.php)
    global $PDO;

    #----- BD CONNECTION
    require 'config/database.php';
    $database = new Database();
    $PDO = $database->connect();
    #--------------------------------------------------------------------

    class CategoriesModel{ 

        static function getAll()
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                # Creamos la query con los parametros recibidos
                $sql = 'SELECT * FROM categoria';
            
                # Preparamos la query con el string generado
                $query = $PDO->query($sql);

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

        static function getAllByProvider($id)
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Si $PDO no es un PDOException, enonces la conexion es correcta y ejecutamos lo siguiente
            if(get_class($PDO) !== 'PDOException'){
                # Creamos la query con los parametros recibidos
                $sql = 'SELECT DISTINCT c.cod_categoria,c.nombre FROM proveedor p
                JOIN proveedor_equipo pe ON p.cod_proveedor = pe.cod_proveedor
                JOIN equipo e ON e.cod_equipo = pe.cod_equipo
                JOIN categoria c ON e.cod_categoria = c.cod_categoria
                WHERE pe.cod_proveedor = :id';

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