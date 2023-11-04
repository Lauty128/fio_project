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
            $equipment = EquipmentsModel::getOne($id);

            // Si $equipment == false significa que la query se ejecuto correctamente pero no existe el equipo buscado
            if($equipment == false){
                DefineError('#-002' ,'No existe el equipo buscado (id='.$id.')');
            }

            return $equipment; 
        }

        static function getSpecifications($id)
        {
            $equipment = EquipmentsModel::getOne($id);

            if(!$equipment){
                DefineError('#-002', 'The indicated equipment was not found');
            }
            
            $filename = $equipment['specifications'] ?? 'default.txt';
            $path = 'resources/specifications/'.$filename;
            
            if(file_exists($path)){
                $custom_filename = $equipment['name'].'.'. explode('.', $filename)[1];

                // Define headers
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$custom_filename");
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                
                // Read the file
                readfile($path);
                exit();
            }
            else{
                DefineError('#-002', 'The equipment specifications file was not found');
            }
            
        }

    }