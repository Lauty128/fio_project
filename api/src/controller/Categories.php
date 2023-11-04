<?php 

    namespace App\Controller;

    //-----> Classes of the project
    use App\Model;

    //-----> Dependencies
    use Flight;

    class Categories{
        
        static function getAll() { 
            # Storege the result of the function getAll() 
            
            $data = Model\Categories::getAll();

            # Return the value to be used in index.php
            Flight::json($data);
            exit();
        }

        static function getAllByProvider(string $id, bool $is_verified = false) { 
            # Verify if the searched provider exists
            # If $is_verified is true, means we verified provider existence previusly
            $verification = ($is_verified)
                ? $is_verified
                : Model\Providers::getOne($id);

            # $verification puede tomar 3 tipos de datos (1 de ellos indica que no existe el proveedor)
                # true = Se paso por parametro que se valido anteriormente la existencia del proveedor
                # ProviderObject = No se valido la existencia del proveedor, anteriormente, pero se valido aqui y es correcta
                # false = Se comprobo la existencia del proveedor y no existe

            if($verification != false){
                # Almacenamos el resultado, con datos de un proveedor que sabemos que existe. Aunque puede que no tenga ninguna categoria
                $data = Model\Categories::getAllByProvider($id);
            }
            else{
                return [
                    'Error' => 404,
                    'Message' => 'The provider you are looking for does not exist'
                ];
            }
            
            # Retornamos el valor para usarlo en index.php
            Flight::json($data);
            exit();
        }

    }