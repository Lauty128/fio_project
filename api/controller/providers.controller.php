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
                $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
                $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT');

            //------------- Manipular queries
                # Definimos un orden por defecto o el recibido por los parametros
                $order = (isset($_GET['order'])) ? formaterOrder($_GET['order']) : constant('ORDER');
                
                # Formateamos las opciones de busqueda recibidas por parametro
                $options = formaterOptionsForProviders($_GET ?? []);
                
                # Obtenemos el offset multiplicando la $page por el $limit
                $offset = $page * $limit;
                
            //-------------- Enviar variables para ejecutar la consulta
            //ProvidersModel::getTotal($options);
            
                $data = ProvidersModel::getAll($offset, $limit, $order, $options);
                # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
                $total = ((count($data) < $limit) && $page == 0)
                    ? count($data)
                    : ProvidersModel::getTotal($options);
            //var_dump($total); exit();

                if(is_int($total) && !isset($data['Error'])){
                    return [
                        'page' => ((int)$page + 1),
                        'limit' => (int)$limit,
                        'hasNextPage' => ((($page + 1) * $limit) < $total),
                        'hasPrevPage' => (($page - 1) >= 0),
                        'total' => (int)$total,
                        'data' => $data
                    ];
                }
                    
                // Si accede aqui es porque ocurri un error
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
            $data = ProvidersModel::getEquipments($id, $offset, $limit);

            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
            ? count($data)
            : ProvidersModel::getTotalByEquipments($id);

            # Retornamos el valor para usarlo en index.php
            if(is_int($total) && !isset($data['Error'])){
                return [
                    'page' => ((int)$page + 1),
                    'limit' => (int)$limit,
                    'hasNextPage' => ((($page + 1) * $limit) < $total),
                    'hasPrevPage' => (($page - 1) >= 0),
                    'total' => (int)$total,
                    'data' => $data
                ];
            }
                
            // Si accede aqui es porque ocurri un error
            return $data;
        }

        static function getOne(int $id)
        {
            $provider = ProvidersModel::getOne($id);
            
            # Si la variable $provider devuelve un error de mensaje, lo devolvemos y cortamos la funcion
            if(isset($provider['Error'])){
                return $provider;
            }

            # Si existe el proveedor ejecutamos el siguiente codigo
            if($provider && !isset($provider['Error'])){
                $categories = CategoriesModel::getAllByProvider($id);
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
            else if($provider === false){
                # Si no existe el proveedor devolvemos el siguiente mensaje
                return [
                    "Error"=>204,
                    "Message"=>"No existe el proveedor buscado"
                ];
            }

            # Si se obtiene un error, devolvemos ese error almacenado en $provider
            return $provider;

        }

    }