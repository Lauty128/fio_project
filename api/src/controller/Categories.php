<?php 

    namespace App\Controller;

    //-----> Clases del proyecto
    use App\Model;

    //-----> Dependencies
    use Flight;

    class Categories{
        
        static function getAll() { 
            # Almacenar el resultado de la función getAll()
            $data = Model\Categories::getAll();

            # Retorna el valor que fue utilizado en index.php
            Flight::json($data);
            exit();
        }

        static function getAllByProvider(string $id, bool $is_verified = false) { 
            # Verifica si exite el proveedor buscado 
            # Si $is_verified es verdadero, decimo que la existencia del proveedor fue validada previamente
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