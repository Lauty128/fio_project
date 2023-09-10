<?php 

    #---- Importar modelos
    require 'model/categories.model.php';

    class CategoriesController{
        
        static function getAll() { 
            //---------- Detectar queries
                # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
                $page = (isset($_GET['PAGE']) && ($_GET['PAGE'] > 0)) ? ($_GET['PAGE'] - 1) : constant('PAGE');
                $limit = (isset($_GET['LIMIT'])) ? $_GET['LIMIT'] : constant('LIMIT');
                
                //---------- Manipular queries
                # Obtenemos el offset multiplicando la $page por el $limit
                $offset = $page * $limit;
                
                # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
                $data = CategoriesModel::getAll($offset, $limit);
                
                # Retornamos el valor para usarlo en index.php
                return $data;
        }

    }