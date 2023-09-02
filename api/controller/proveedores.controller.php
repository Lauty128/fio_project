<?php

    #---- Importar modelos
    require 'model/proveedores.model.php';

    class ProveedoresController{
    
        static function getAll(int $page, int $limit, bool $equipments)
        {
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;

            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            $data = ProveedoresModel::getAll($offset, $limit, $equipments);
            
            # Retornamos el valor para usarlo en index.php
            return $data;
            
        }
        
        static function getEquipments(int $id, int $page, int $limit)
        {
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