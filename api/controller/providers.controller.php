<?php
    
    #---- Utilidades
    require 'utils/queries.util.php';
    require 'utils/parameters.util.php';

    #---- Importar modelos
    require 'model/categories.model.php';
    require 'model/providers.model.php';

    class ProvidersController{

        static function getAll()
        {
        //------------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : PAGE;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : LIMIT;

        //------------- Manipular queries
            # Definimos un orden por defecto o el recibido por los parametros
            $order = (isset($_GET['order'])) ? formaterOrder($_GET['order']) : ORDER;
            
            # Formateamos las opciones de busqueda recibidas por parametro
            $options = formaterOptionsForProviders($_GET ?? []);
            
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;
                
        //-------------- Enviar variables para ejecutar la consulta
            //ProvidersModel::getTotal($options);
            
            // Cuando ejecutamos el modelo, una consulta correcta es devuelta.
            // En caso de ocurrir un error, el mismo es manejado y devuelto, cortando la ejecucion del codigo.
            $data = ProvidersModel::getAll($offset, $limit, $order, $options);
            
            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
                ? count($data)
                : ProvidersModel::getTotal($options);

            # If an error occurred the code execution is cut off and an error message is returned
            # The code is prepare for returning a success response
            return [
                'page' => ((int)$page + 1),
                'limit' => (int)$limit,
                'hasNextPage' => ((($page + 1) * $limit) < $total),
                'hasPrevPage' => (($page - 1) >= 0),
                'total' => (int)$total,
                'data' => $data
            ];
        }

        static function getEquipments(int $id)
        {
        //---------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : PAGE;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : SMALL_LIMIT;
            $exist = (isset($_GET['exist']) && $_GET['exist'] == 1) ? true : false;

        //---------- Manipular queries
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;

            if(!$exist){
                $provider = ProvidersModel::getOne($id);
                if($provider === false){
                    DefineError('#-002', 'No existe el proveedor solicitado');
                }
            }

            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $data = ProvidersModel::getEquipments($id, $offset, $limit);
            
            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
            ? count($data)
            : ProvidersModel::getTotalByEquipments($id);

            # Retornamos el valor para usarlo en index.php
            return [
                'page' => ((int)$page + 1),
                'limit' => (int)$limit,
                'hasNextPage' => ((($page + 1) * $limit) < $total),
                'hasPrevPage' => (($page - 1) >= 0),
                'total' => (int)$total,
                'data' => $data
            ];
        }

        static function getOne(int $id)
        {
            $provider = ProvidersModel::getOne($id);

            // Si $provider == false significa que la query se ejecuto correctamente pero no existe el proveedor buscado
            if($provider == false){
                DefineError('#-002' ,'No existe el proveedor buscado (id='.$id.')');
            }

            # Si existe el proveedor ejecutamos el siguiente codigo
            $categories = CategoriesModel::getAllByProvider($id, true);
            # Devolvemos un objeto con los datos del proveeedor y con una propiedad llamada equipos
            # que contiene un array con todos los equipos que vende el proveedor
            return [
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
            ];

        }

    }