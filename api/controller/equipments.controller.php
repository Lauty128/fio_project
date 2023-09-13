<?php 

    #------ Importar Utilidades
        # estas se usan en los controladores
        include 'utils/parameters.util.php';
        # estas se usan en los modelos
        require 'utils/queries.util.php';

    #------ Importar modelos
        require 'model/Equipments.model.php';

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
                
                # Obtenemos el offset multiplicando la $page por el $limit
                $offset = $page * $limit;
            
            //-------------- Enviar variables para ejecutar la consulta
                # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
                $data = EquipmentsModel::getAll($offset, $limit, $order , $options);
                
                # Retornamos el valor para usarlo en index.php
                return $data;
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
                $response = EquipmentsModel::getProviders($id, $offset, $limit);

                # Retornamos el valor para usarlo en index.php
                return $response;
        }

        static function getOne(int $id)
        {
            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $response = EquipmentsModel::getOne($id);

            # Retornamos el valor para usarlo en index.php
            return $response;
        }

    }