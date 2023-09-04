<?php

    #---- Importar modelos
    require 'model/proveedores.model.php';

    class ProveedoresController{
    
        static function getAll()
        {
            //---------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT');
            $equipments = (isset($_GET['equipos']) && $_GET['equipos'] == '1');

            //---------- Manipular queries
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;

            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            $data = ProveedoresModel::getAll($offset, $limit, $equipments);
            
            # Retornamos el valor para usarlo en index.php
            return $data;
        }
        
        static function getEquipments(int $id)
        {
            //---------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT_SMALL');

            //---------- Manipular queries
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;

            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $response = ProveedoresModel::getEquipments($id, $offset, $limit);
                
            # Retornamos el valor para usarlo en index.php
            return $response;
        }

        static function getOne(int $id)
        {            
            $provider = ProveedoresModel::getOne($id);
            
            # Si la variable $provider devuelve un error de mensaje, lo devolvemos y cortamos la funcion
            if(isset($provider['Error'])){
                return $provider;
            }

            # Si existe el proveedor ejecutamos el siguiente codigo
            if(count($provider) == 1){
                $equipments = ProveedoresModel::getEquipments($id);

                # Devolvemos un objeto con los datos del proveeedor y con una propiedad llamada equipos
                # que contiene un array con todos los equipos que vende el proveedor
                return [
                    ...$provider[0],
                    "equipos" => $equipments
                ];
            }
            else{
                # Si no existe el proveedor devolvemos el siguiente mensaje
                return [
                    "Error"=>400,
                    "Message"=>"No existe el proveedor buscado"
                ];
            }
            
        }

    }