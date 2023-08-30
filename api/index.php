<?php
    # ------------ Importar código de flightphp
    require 'vendor/flightphp/flight/Flight.php';

    # Importar variables
    require_once 'config/variables.php';

    # ------------ Importar los controladores
    require 'controller/proveedores.controller.php';
    

    #******************* ENDPOINTS **************************
    # --- Obtener todos los proveedores
    Flight::route('/proveedores', function(){

        # Si existe el parametro page,  entonces se usa ese valor, si no el definido en el archivo variables.php
        $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? ($_GET['page'] - 1) : constant('PAGE');
        # Si existe el parametro limit, entonces se usa ese valor, si no el definido en el archivo variables.php
        $limit = (isset($_GET['limit'])) ? $_GET['limit'] : constant('LIMIT');

        # Almacenar el resultado de la funcion getAll() de la clase ProveedoresControllers en $data
        $data = ProveedoresController::getAll($page, $limit);
        
        # Ejecutar la funcion JSON de FlightPHP para devolver un JSON formateado
        Flight::json($data);
    });
    
    # --- Obtener un proveedor
    Flight::route('/proveedores/@id', function($id){
        echo 'Hola mundo desde "Proveedores" ('.$id.')';
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
