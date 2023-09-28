<?php 

    #------ Importar Utilidades
        # estas se usan en los controladores
        require 'utils/parameters.util.php';
        # estas se usan en los modelos
        require 'utils/queries.util.php';

    #------ Importar modelos
        require 'model/equipments.model.php';

    class EquipmentsController{
        
        static function getAll() { 
            //-------------- Detectar queries
                # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
                $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
                $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT');

            //-------------- Manipular queries
                # Definimos un orden por defecto o el recibido por los parametros
                $order = (isset($_GET['order'])) ? formaterOrder($_GET['order']) : constant('ORDER');
                
                # Formateamos las opciones de busqueda recibidas por parametro
                $options = formaterOptionsForEquipments($_GET ?? []);
                // var_dump($options); exit();
                # Obtenemos el offset multiplicando la $page por el $limit
                $offset = $page * $limit;
            
            //-------------- Enviar variables para ejecutar la consulta
                # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
                $equipments = EquipmentsModel::getAll($offset, $limit, $order, $options);

                # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
                $total = ((count($equipments) < $limit) && $page == 0)
                    ? count($equipments)
                    : EquipmentsModel::getTotal($options);
                
                if(is_int($total) && !isset($provider['Error'])){
                    return [
                        'page' => ((int)$page + 1),
                        'limit' => (int)$limit,
                        'hasNextPage' => ((($page + 1) * $limit) < $total),
                        'hasPrevPage' => (($page - 1) >= 0),
                        'total' => (int)$total,
                        'data' => $equipments
                    ];
                }
                else{
                    return [
                        'Error' => 500,
                        'Message' => 'An error was detected'
                    ];
                }

                //return $data;
                
        }

        static function getProviders(int $id)
        {
            //-------------- Detectar queries
                # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
                $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
                $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT_SMALL');

            //-------------- Manipular queries
                # Obtenemos el offset multiplicando la $page por el $limit
                $offset = $page * $limit;
            
            //-------------- Enviar variables para ejecutar la consulta
                # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
                $providers = EquipmentsModel::getProviders($id, $offset, $limit);

                # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
                $total = ((count($providers) < $limit) && $page == 0)
                    ? count($providers)
                    : EquipmentsModel::getTotalByProviders($id);

                # Retornamos el valor para usarlo en index.php
                if(is_int($total) && !isset($providers['Error'])){
                    return [
                        'page' => ((int)$page + 1),
                        'limit' => (int)$limit,
                        'hasNextPage' => ((($page + 1) * $limit) < $total),
                        'hasPrevPage' => (($page - 1) >= 0),
                        'total' => (int)$total,
                        'data' => $providers
                    ];
                }
                else{
                    return [
                        'Error' => 500,
                        'Message' => 'An error was detected'
                    ];
                }
        }

        static function getOne(int $id)
        {
            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $response = EquipmentsModel::getOne($id);

            # Retornamos el valor para usarlo en index.php
            return $response;
        }

    }