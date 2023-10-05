<?php 

    #---- Importar modelos
    require 'model/categories.model.php';
    require 'model/providers.model.php';

    class CategoriesController{
        
        static function getAll() { 
            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            
            $data = CategoriesModel::getAll();

            # Retornamos el valor para usarlo en index.php
            return $data;
        }

        static function getAllByProvider(string $id, bool $is_verified = false) { 
            # Verificar que existe el proveedor buscado
            # Si $is_verified es true, significa que anteriormente verificamos que el proveedor existe
            $verification = ($is_verified)
                ? $is_verified
                : ProvidersModel::getOne($id);

            # $verification puede tomar 3 tipos de datos (1 de ellos indica que no existe el proveedor)
                # true = Se paso por parametro que se valido anteriormente la existencia del proveedor
                # ProviderObject = No se valido la existencia del proveedor, anteriormente, pero se valido aqui y es correcta
                # false = Se comprobo la existencia del proveedor y no existe

            if($verification != false){
                # Almacenamos el resultado, con datos de un proveedor que sabemos que existe. Aunque puede que no tenga ninguna categoria
                $data = CategoriesModel::getAllByProvider($id);
            }
            else{
                return [
                    'Error' => 404,
                    'Message' => 'The provider you are looking for does not exist'
                ];
            }
            
            # Retornamos el valor para usarlo en index.php
            return $data;
        }

    }