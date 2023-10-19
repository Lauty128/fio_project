<?php
    # ------------ Importar código de flightphp
    require 'vendor/flightphp/flight/Flight.php';

    # ------------ Importar variables
    require 'config/app.php';

    #------------- BD PDO
    $PDO; 
    
    #******************* ENDPOINTS **************************
    # --- Obtener todos los proveedores
    Flight::route('/providers', function(){
        require 'controller/providers.controller.php';
        $response = ProvidersController::getAll();
        Flight::json($response);
    });
    
    # --- Obtener un proveedor
    Flight::route('/providers/@id', function($id){
        require 'controller/providers.controller.php';
        $response = ProvidersController::getOne($id);
        Flight::json($response);
    });

    # --- Obtener los equipos vendidos por un proveedor
    Flight::route('/providers/@id/equipments', function($id){
        require 'controller/providers.controller.php';
        $response = ProvidersController::getEquipments($id);
        Flight::json($response);
    });
    
    # --- Obtener todos los equipos
    Flight::route('/equipments', function(){
        require 'controller/equipments.controller.php';
        $response = EquipmentsController::getAll();
        Flight :: json($response);
    });
    
    # --- Obtener un equipo
    Flight::route('/equipments/@id', function($id){
        require 'controller/equipments.controller.php';
        $response = EquipmentsController::getOne($id);
        Flight::json($response);
    });

    # --- Obtener los proveedores que venden un equipo
    Flight::route('/equipments/@id/providers', function($id){
        require 'controller/equipments.controller.php';
        $response = EquipmentsController::getProviders($id);
        Flight::json($response);
    });

    # --- Obtener todas las categorias
    Flight::route('/categories', function(){
        require 'controller/categories.controller.php';
        $response = CategoriesController::getAll();
        Flight::json($response);
    });

    # --- Obtener todas las categorias de equipos que vende un proveedor
    Flight::route('/categories/@id', function($id){
        require 'controller/categories.controller.php';
        $response = CategoriesController::getAllByProvider($id);
        Flight::json($response);
    });
    
    # Code error = "Not Found endpoint #-404"
    # This error is showed if the endpoint not exists.
    Flight::route('*', function(){
        $response = DefineError('#-404', 'The requested endpoint is not found');
        Flight::json($response);
    });

    # ------------ Iniciar API
    Flight::start();
