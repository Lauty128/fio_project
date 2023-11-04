<?php
    # ------------------------ NO ELIMINAR -----------------------
        # Importar código de flightphp
        require 'vendor/flightphp/flight/Flight.php';
        
        # Importar variables
        require 'config/app.php';

        # Importar la clase para conectarse a la base de datos
        require 'config/database.php';
    # ----------------------------------------------------------
    
    //------------> Global Middleware
    require 'middleware/auth.php';
    use Middleware\Auth;

    function validateAuthentication(){
        if(!Auth::VerifyAuthenication()){
            DefineError('#-401', 'Authentication is required for this application');
        }
    }
    

    #******************* ENDPOINTS **************************
        # --- Obtener todos los proveedores
        Flight::route('/providers', function(){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/providers.controller.php';
            $response = ProvidersController::getAll();
        
            //-----> Response
            Flight::json($response);
        });
        
        # --- Obtener un proveedor
        Flight::route('/providers/@id', function($id){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/providers.controller.php';
            $response = ProvidersController::getOne($id);
            
            //-----> Response
            Flight::json($response);
        });

        # --- Obtener los equipos vendidos por un proveedor
        Flight::route('/providers/@id/equipments', function($id){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/providers.controller.php';
            $response = ProvidersController::getEquipments($id);
            
            //-----> Response
            Flight::json($response);
        });
        
        # --- Obtener todos los equipos
        Flight::route('/equipments', function(){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/equipments.controller.php';
            $response = EquipmentsController::getAll();
            
            //-----> Response
            Flight::json($response);
        });
        
        # --- Obtener un equipo
        Flight::route('/equipments/@id', function($id){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/equipments.controller.php';
            $response = EquipmentsController::getOne($id);
            
            //-----> Response
            Flight::json($response);
        });

         # --- Obtener las especificaciones de un equipo
         Flight::route('/equipments/@id/specifications', function($id){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/equipments.controller.php';
            EquipmentsController::getSpecifications($id);
            
        });

        # --- Obtener los proveedores que venden un equipo
        Flight::route('/equipments/@id/providers', function($id){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/equipments.controller.php';
            $response = EquipmentsController::getProviders($id);
            
            //-----> Response
            Flight::json($response);
        });

        # --- Obtener todas las categorias
        Flight::route('/categories', function(){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/categories.controller.php';
            $response = CategoriesController::getAll();
            
            //-----> Response
            Flight::json($response);
        });

        # --- Obtener todas las categorias de equipos que vende un proveedor
        Flight::route('/categories/@id', function($id){
            //-----> Middlewares
            validateAuthentication();
            
            //-----> Controllers    
            require 'controller/categories.controller.php';
            $response = CategoriesController::getAllByProvider($id);
            
            //-----> Response
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
