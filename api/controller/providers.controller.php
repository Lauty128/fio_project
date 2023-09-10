<?php
    
    #---- Utilidades
    require 'utils/queries.util.php';
    require 'utils/parameters.util.php';

    #---- Importar modelos
    require 'model/providers.model.php';

    class ProvidersController{

        static function getAll()
        {
            //------------- Detectar queries
                # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
                $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
                $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT');
                //$equipments = (isset($_GET['equipments']) && $_GET['equipos'] == '1');

            //------------- Manipular queries
                # Definimos un orden por defecto o el recibido por los parametros
                $order = (isset($_GET['order'])) ? formaterOrder($_GET['order']) : constant('ORDER');
                
                # Formateamos las opciones de busqueda recibidas por parametro
                $options = formaterOptions($_GET ?? []);
                
                # Obtenemos el offset multiplicando la $page por el $limit
                $offset = $page * $limit;
                
            //-------------- Enviar variables para ejecutar la consulta
                # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
                $data = ProvidersModel::getAll($offset, $limit, $order, $options);
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
            $response = ProvidersModel::getEquipments($id, $offset, $limit);

            # Retornamos el valor para usarlo en index.php
            return $response;
        }

        static function getOne(int $id)
        {
            $provider = ProvidersModel::getOne($id);
            // var_dump($provider);exit();
            # Si la variable $provider devuelve un error de mensaje, lo devolvemos y cortamos la funcion
            if(isset($provider['Error'])){
                return $provider;
            }

            # Si existe el proveedor ejecutamos el siguiente codigo
            if(count($provider) == 1){
                $equipments = ProvidersModel::getEquipments($id);

                # Devolvemos un objeto con los datos del proveeedor y con una propiedad llamada equipos
                # que contiene un array con todos los equipos que vende el proveedor
                return [
                    // *ACTUALIZAR PHP PARA HACER ESTO*
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