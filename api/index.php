<?php
    
    //-------------> Dependencies 
    # Autoload
    require __DIR__ . '/vendor/autoload.php';
    
    // $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    // $dotenv->load();

    # Configs
    require __DIR__ . '/public/config.php';
    
    //-------------> Global Middlewares
    //Flight::Validate();
    
    // use Illuminate\Hashing\BcryptHasher;
    // $hasher = new BcryptHasher();
    // var_dump($hasher->check('123', '$2y$10$hH7cOCRaJnG4ki1fdRmuA.KQVyETT8NcE2J5ICZx6oMfU5bYWMIM6'));
    // exit();
    
    //-------------> Routes
    #-----> Providers
    # Obtener listado de proveedores
    Flight::route('GET /providers', function(){ \App\Controller\Providers::getAll(); });
    # Obtener informacion completa de un proveedor
    Flight::route('GET /providers/@id', function($id){ \App\Controller\Providers::getOne($id); });
    # Obtener equipos medicos que comercializa un proveedor
    Flight::route('GET /providers/@id/equipments', function($id){ \App\Controller\Providers::getEquipments($id); });
    
    #-----> Equipments
    # Listado de equipos medicos
    Flight::route('GET /equipments', function(){ \App\Controller\Equipments::getAll(); });
    # Descargar excel con lista de equipos medicos
    Flight::route('GET /equipments/template', function(){ \App\Controller\Equipments::getTemplate(); });
    # Obtener datos de un equipo medico
    Flight::route('GET /equipments/@id', function($id){ \App\Controller\Equipments::getOne($id); });
    # Obtener proveedores que venden un equipo medico
    Flight::route('GET /equipments/@id/providers', function($id){ \App\Controller\Equipments::getProviders($id); });
    # Descargar especificaciones de un equipo medico
    Flight::route('GET /equipments/@id/specifications', function($id){ \App\Controller\Equipments::getSpecifications($id); });

    #-----> Categories
    # Obtener listado de categorias
    Flight::route('GET /categories', function(){ \App\Controller\Categories::getAll(); });
    # Obtener categorias que comercializa un proveedor
    Flight::route('GET /categories/@id', function($id){ \App\Controller\Categories::getAllByProvider($id); });

    #-----> Backups
    # Obtener listado de backups guardados en el sistema
    Flight::route('GET /backups', function(){ \App\Controller\Backup::getAll(); });
    # Descargar excel con los datos actuales del sistema
    Flight::route('GET /backups/template', function(){ \App\Controller\Backup::downloadTemplate(); });
    # Descargar excel con los datos del backup indicado (template de la fecha indicada)
    Flight::route('GET /backups/template/@date', function($date){ \App\Controller\Backup::downloadOldTemplate($date); });
    # Crear backup y actualizar base de datos con archivo excel subido
    Flight::route('POST /backups/update', function(){ \App\Controller\Backup::submitFile(); });
    # Actualizar base de datos con backup de la fecha indicada
    Flight::route('GET /backups/update/file/@date', function($date){ \App\Controller\Backup::updateDatabase($date); });
    # Eliminar backup de la fecha indicada (Si esta activo el backup no se elimina)
    Flight::route('GET /backups/delete/@date', function($date){ \App\Controller\Backup::deleteBackup($date); });

    # Users
    //Flight::route('POST /auth', function(){ \App\Controller\Auth::login(); });


    # Not found pagina
    Flight::route('*', function(){
        $response = \App\Config\Config::DefineError('#-404', 'The requested endpoint is not found');
        Flight::json($response);
    });

    //-------------> Execute API
    Flight::start();