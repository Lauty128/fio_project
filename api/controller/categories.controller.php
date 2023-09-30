<?php 

    #---- Importar modelos
    require 'model/categories.model.php';

    class CategoriesController{
        
        static function getAll() { 
            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            
            $data = CategoriesModel::getAll();

            # Retornamos el valor para usarlo en index.php
            return $data;
        }

        static function getAllByProviders(string $id) { 
            //---------- Detectar queries
            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            $data = CategoriesModel::getAllByProvider($id);
            
            # Retornamos el valor para usarlo en index.php
            return $data;
        }

    }