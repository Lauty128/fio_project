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
            
            if(isset($provider['Error'])){
                return $provider;
            }


            if(count($provider) == 1){
                $equipments = ProveedoresModel::getEquipments($id);

                return [
                    ...$provider[0],
                    "equipos" => $equipments
                ];
            }
            else{
                return [
                    "Error"=>000,
                    "Message"=>"No existe el proveedor buscado"
                ];
            }
            
        }

    }