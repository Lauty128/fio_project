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
   Flight::route('/proveedores', function(){
        $response = ProveedoresController::getAll();
        Flight::json($response);
    });
    
    # --- Obtener un proveedor
    Flight::route('/proveedores/@id', function($id){
        $response = ProveedoresController::getOne($id);
        Flight::json($response);
    });

    # --- Obtener un proveedor
    Flight::route('/proveedores/@id/equipos', function($id){
        $response = ProveedoresController::getEquipments($id);
        Flight::json($response);
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
