<?php

    #---- Importar modelos
    require 'model/proveedores.model.php';

    class ProveedoresController{
    
        static function getAll(int $page, int $limit, $category = null)
        {
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;
            # Esto es para que no tire error de variable sin usar
            $category = $category;

            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            $data = ProveedoresModel::getAll($offset, $limit);
            
            # Retornamos el valor para usarlo en index.php
            return $data;
        }
        
        static function getAll_byEquipment($equipment, $page = 0, $limit = 30){
            # Use the PDO variable of the line 6
            global $PDO;

        }

    }