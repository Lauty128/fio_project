<?php
    # ------------ Importar código de flightphp
    require 'vendor/flightphp/flight/Flight.php';

    # Importar variables
    require_once 'config/variables.php';

    # ------------ Importar los controladores
    require 'controller/proveedores.controller.php';
    

    # ----------- CONFIGURAR CORS
    // Specify domains from which requests are allowed
    header('Access-Control-Allow-Origin: *');
    // Specify which request methods are allowed
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    // Additional headers which may be sent along with the CORS request
    header('Access-Control-Allow-Headers: X-Requested-With,Authorization,Content-Type');

    #******************* ENDPOINTS **************************
    # --- Obtener todos los proveedores
   Flight::route('/proveedores', function()
    {
        # Si existe el parametro page,  entonces se usa ese valor, si no el definido en el archivo variables.php
        $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
        # Si existe el parametro limit, entonces se usa ese valor, si no el definido en el archivo variables.php
        $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT');
        # Si existe el parametro limit, entonces se usa ese valor, si no el definido en el archivo variables.php
        $equipments = (isset($_GET['equipos']) && $_GET['equipos'] == '1');
    
        # Almacenar el resultado de la funcion getAll() de la clase ProveedoresControllers en $data
        $data = ProveedoresController::getAll($page, $limit, $equipments);
        
        # Ejecutar la funcion JSON de FlightPHP para devolver un JSON formateado
        Flight::json($data);
    });
    
    # --- Obtener un proveedor
    Flight::route('/proveedores/@id', function($id)
    {
        $data = ProveedoresController::getOne($id);

        Flight::json($data);
    });

    # --- Obtener un proveedor
    Flight::route('/proveedores/@id/equipos', function($id)
    {
        # Si existe el parametro page,  entonces se usa ese valor, si no el definido en el archivo variables.php
        $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
        # Si existe el parametro limit, entonces se usa ese valor, si no el definido en el archivo variables.php
        $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT_SMALL');

        $data = ProveedoresController::getEquipments($id, $page, $limit);

        Flight::json($data);
    });
    
    # --- Obtener todos los equipos
    Flight::route('/equipos', function(){
        echo 'Hola mundo desde "Equipos"';
    });
    
    # --- Obtener un equipo
    Flight::route('/equipos/@id', function($id){
        echo 'Hola mundo desde "Equipos" ('.$id.')';
    });
    
    # --- Obtener todas las categorias
    Flight::route('/categorias', function(){
        echo 'Hola mundo desde "Categorias"';
    });
    
    #************************************************

    # ------------ Iniciar API
    Flight::start();
