<?php

    namespace App\Controller;

    //-----> Classes
    use App\Util;
    use App\Model;
    use App\Config;
    use App\Util\Backup\Generator;

//-----> Dependencies
    use Flight;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\IOFactory;


    class Equipments{

        #################################################################################
        ######################### OBTENER TODOS LOS EQUIPOS #############################
        #################################################################################
        static function getAll()
        { 
        //-------------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : Config\Config::PAGE;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : Config\Config::LIMIT;

        //-------------- Manipular queries
            # Definimos un orden por defecto o el recibido por los parametros
            $order = (isset($_GET['order'])) ? Util\Parameters::formaterOrder($_GET['order']) : Config\Config::ORDER;
            
            # Formateamos las opciones de busqueda recibidas por parametro
            $options = Util\Parameters::formaterOptionsForEquipments($_GET ?? []);
            
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;
        
        //-------------- Enviar variables para ejecutar la consulta
            # Almacenamos el resultado de la funcion getAll() mandando los parametros que pide
            $data = Model\Equipments::getAll($offset, $limit, $order, $options);

            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
                ? count($data)
                : Model\Equipments::getTotal($options);
            
            # Si un error ocurre se detiene la ejecucion del codigo y se devuelve un mensaje de error.
            # El codigo esta preparado para devolver una respuesta de exito.
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

        ############################################################################
        ################## OBTENER PROVEEDORES QUE VENDEN UN EQUIPO ################
        ############################################################################
        static function getProviders(int $id)
        {
        //-------------- Detectar queries
            # Si existen los parametros toman su valor, sino el valor por defecto en las configuraciones
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : Config\Config::PAGE;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : Config\Config::SMALL_LIMIT;

        //-------------- Manipular queries
            # Obtenemos el offset multiplicando la $page por el $limit
            $offset = $page * $limit;
        
        //-------------- Enviar variables para ejecutar la consulta
            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $data = Model\Equipments::getProviders($id, $offset, $limit);

            # Si la cantidad de elementos es menor al limite o igual a cero, no se consulta en la base de datos el total de elementos
            $total = ((count($data) < $limit) && $page == 0)
                ? count($data)
                : Model\Equipments::getTotalByProviders($id);

            
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

        ##########################################################################
        ######################### OBTENER UN EQUIPO ##############################
        ##########################################################################
        static function getOne(int $id)
        {
        //------------- Ejecutar consulta
            # Almacenamos el resultado de la funcion getEquipments() mandando los parametros que pide
            $equipment = Model\Equipments::getOne($id);

            // Si $equipment == false significa que la query se ejecuto correctamente pero no existe el equipo buscado
            if($equipment == false){
                Config\Config::DefineError('#-002' ,'No existe el equipo buscado (id='.$id.')');
            }

            Flight::json($equipment);
            exit();
        }

        static function getSpecifications($id)
        {
            $equipment = Model\Equipments::getOne($id);

            if(!$equipment){
                Config\Config::DefineError('#-002', 'The indicated equipment was not found');
            }
            
            $filename = $equipment['specifications'] ?? 'default.txt';
            $path = 'files/specifications/'.$filename;
            
            if(file_exists($path)){
                $custom_filename = $equipment['name'].'.'. explode('.', $filename)[1];

                // Headers definidos para la descarga correcta del archivo
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$custom_filename");
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                
                // LEER EL DOCUMENTO
                readfile($path);
                exit();
            }
            else{
                Config\Config::DefineError('#-002', 'Las especificaciones del equipo no fueron encontradas');
            }
            
        }

        static function getTemplate()
        {
            $offset = (isset($_GET['offset'])) ? $_GET['offset'] : 0;
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 10;

            $data = Model\Equipments::getAllFull($offset, $limit, 'ID-ASC', null);

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                    ->setCreator('Pems')
                    ->setTitle('Template para actualizar la base de datos');

            # Define la primera pagina y la define como activa
            $activeSheet = $spreadsheet->getActiveSheet();
            # Se indica el nombre a la pagina
            $activeSheet->setTitle('Equipos');

            Generator::header($activeSheet, 'equipment');
            Generator::writeEquipments($activeSheet, $data);

            $nombre = "Pems (" . ($offset + 1) . "-" . ($offset + $limit) . ")" . ".xlsx";
            # Estas cabeceras permiten configurar la descarga  del archivo
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $nombre . '"');
        
            # Esto activa la descarga del archivo desde el navegador.
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit();
        }

    }