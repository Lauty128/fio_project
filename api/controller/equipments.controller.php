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
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : PAGE;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : LIMIT;

        //-------------- Manipular queries
            # Definimos un orden por defecto o el recibido por los parametros
            $order = (isset($_GET['order'])) ? formaterOrder($_GET['order']) : ORDER;
            
            # Formateamos las opciones de busqueda recibidas por parametro
            $options = formaterOptionsForEquipments($_GET ?? []);
            
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;
        
        //-------------- Enviar variables para ejecutar la consulta
            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            $data = EquipmentsModel::getAll($offset, $limit, $order, $options);

            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
                ? count($data)
                : EquipmentsModel::getTotal($options);
            
            if(!isset($data['Error'])){
                return [
                    'page' => ((int)$page + 1),
                    'limit' => (int)$limit,
                    'hasNextPage' => ((($page + 1) * $limit) < $total),
                    'hasPrevPage' => (($page - 1) >= 0),
                    'total' => (int)$total,
                    'data' => $data
                ];
            }
            
            // Si accede aqui es porque ocurrio un error
            return $data;
        }

        static function getProviders(int $id)
        {
            //-------------- Detectar queries
                # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
                $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : PAGE;
                $limit = (isset($_GET['limit'])) ? $_GET['limit'] : SMALL_LIMIT;

            //-------------- Manipular queries
                # Obtenemos el offset multiplicando la $page por el $limit
                $offset = $page * $limit;
            
            //-------------- Enviar variables para ejecutar la consulta
                # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
                $data = EquipmentsModel::getProviders($id, $offset, $limit);

                # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
                $total = ((count($data) < $limit) && $page == 0)
                    ? count($data)
                    : EquipmentsModel::getTotalByProviders($id);

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
                
                // Si accede aqui es porque ocurrio un error
                return $data;       
        }

        static function getOne(int $id)
        {
            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $response = EquipmentsModel::getOne($id);

            if($response && !isset($response['Error'])){
                # Retornamos el valor para usarlo en index.php
                return $response;
            }
            else if($response == false){
                return [
                    "Error"=>204,
                    "Message"=>"No existe el equipo buscado"
                ];
            }

            # Si se obtiene un error, devolvemos ese error almacenado en $provider
            return $response;
        }

    }