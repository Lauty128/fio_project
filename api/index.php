<?php
    # ------------ Importar código de flightphp
    require 'vendor/flightphp/flight/Flight.php';

    #**************** ENDPOINTS ***********************

    # --- Obtener todos los proveedores
    Flight::route('/proveedores', function(){
        echo 'Hola mundo desde "Proveedores"';
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