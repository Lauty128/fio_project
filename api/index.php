<?php
    # ------------ Importar código de flightphp
    require 'vendor/flightphp/flight/Flight.php';

    #**************** ENDPOINTS ***********************

    # --- Obtener todos los proveedores
    Flight::route('/proveedor', function(){
        echo 'Hello world from Proveedor';
    });
    
    # --- Obtener un proveedor
    Flight::route('/proveedor/@id', function($id){
        echo 'Hello world from Proveedor ('.$id.')';
    });
    
    # --- Obtener todos los equipos
    Flight::route('/equipos', function(){
        echo 'Hello world from Equipo';
    });
    
    # --- Obtener un equipo
    Flight::route('/equipos/@id', function($id){
        echo 'Hello world from Equipo ('.$id.')';
    });
    
    # --- Obtener todas las categorias
    Flight::route('/categorias', function(){
        echo 'Hello world from Categorias';
    });
    
    #************************************************

    # ------------ Iniciar API
    Flight::start();