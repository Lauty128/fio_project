<?php

    namespace App\Controller;

    //-----> Classes of the project
    use App\Util;
    use App\Model;
    use App\Config;

    //-----> Dependencies
    use Flight;

    class Providers{

        ################################################################
        ######################### GET ALL ##############################
        ################################################################
        static function getAll()
        {
        //------------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : Config\Config::PAGE;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : Config\Config::LIMIT;

        //------------- Manipular queries
            # Definimos un orden por defecto o el recibido por los parametros
            $order = (isset($_GET['order'])) ? Util\Parameters::formaterOrder($_GET['order']) : Config\Config::ORDER;
            
            # Formateamos las opciones de busqueda recibidas por parametro
            $options = Util\Parameters::formaterOptionsForProviders($_GET ?? []);
            
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;
                
        //-------------- Enviar variables para ejecutar la consulta
            // Cuando ejecutamos el modelo, una consulta correcta es devuelta.
            // En caso de ocurrir un error, el mismo es manejado y devuelto, cortando la ejecucion del codigo.
            $data = Model\Providers::getAll($offset, $limit, $order, $options);
            
            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
                ? count($data)
                : Model\Providers::getTotal($options);
            
            # The code is prepare for returning a success response
            Flight::json([
                'page' => ((int)$page + 1),
                'limit' => (int)$limit,
                'hasNextPage' => ((($page + 1) * $limit) < $total),
                'hasPrevPage' => (($page - 1) >= 0),
                'total' => (int)$total,
                'data' => $data
            ]);
            exit();
        }

        ################################################################
        ################# GET EQUIPMENTS BY A PROVIDER #################
        ################################################################
        static function getEquipments(int $id)
        {
        //---------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : Config\Config::PAGE;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : Config\Config::SMALL_LIMIT;

        //---------- Manipular queries
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;

            $provider = Model\Providers::getOne($id);
            if($provider === false){
                Config\Config::DefineError('#-002', 'No existe el proveedor solicitado');
            }
            

            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $data = Model\Providers::getEquipments($id, $offset, $limit);
            
            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
            ? count($data)
            : Model\Providers::getTotalByEquipments($id);

            # Retornamos el valor para usarlo en index.php
            Flight::json([
                'page' => ((int)$page + 1),
                'limit' => (int)$limit,
                'hasNextPage' => ((($page + 1) * $limit) < $total),
                'hasPrevPage' => (($page - 1) >= 0),
                'total' => (int)$total,
                'data' => $data
            ]);
            exit();
        }

        ################################################################
        ######################### GET ONE ##############################
        ################################################################
        static function getOne(int $id)
        {
            $provider = Model\Providers::getOne($id);

            // Si $provider == false significa que la query se ejecuto correctamente pero no existe el proveedor buscado
            if($provider == false){
                Config\Config::DefineError('#-002' ,'No existe el proveedor buscado (id='.$id.')');
            }

            # Si existe el proveedor ejecutamos el siguiente codigo
            $categories = Model\Categories::getAllByProvider($id, true);
            # Devolvemos un objeto con los datos del proveeedor y con una propiedad llamada equipos
            # que contiene un array con todos los equipos que vende el proveedor
            Flight::json([
                /*
                EN CASO DE NO TENER LA VERSION MAS ACTUALIZADA DE PHP
                REEMPLAZAR "...$providers," POR:
                "name" => $provider["name"],
                "web" => $provider["web"],
                "mail" => $provider["mail"],
                "address" => $provider["address"],
                "phone" => $provider["phone"],
                */
                ...$provider,
                'categories' => $categories
            ]);
            exit();
        }

    }