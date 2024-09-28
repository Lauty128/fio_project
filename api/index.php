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
    # Providers
    Flight::route('GET /providers', function(){ \App\Controller\Providers::getAll(); });
    Flight::route('GET /providers/@id', function($id){ \App\Controller\Providers::getOne($id); });
    Flight::route('GET /providers/@id/equipments', function($id){ \App\Controller\Providers::getEquipments($id); });
    
    # Equipments
    Flight::route('GET /equipments', function(){ \App\Controller\Equipments::getAll(); });
    Flight::route('GET /equipments/template', function(){ \App\Controller\Equipments::getTemplate(); });
    Flight::route('GET /equipments/@id', function($id){ \App\Controller\Equipments::getOne($id); });
    Flight::route('GET /equipments/@id/providers', function($id){ \App\Controller\Equipments::getProviders($id); });
    Flight::route('GET /equipments/@id/specifications', function($id){ \App\Controller\Equipments::getSpecifications($id); });

    # Categories
    Flight::route('GET /categories', function(){ \App\Controller\Categories::getAll(); });
    Flight::route('GET /categories/@id', function($id){ \App\Controller\Categories::getAllByProvider($id); });

    # Backups
    Flight::route('GET /backups', function(){ \App\Controller\Backup::getAll(); });
    Flight::route('GET /backups/template', function(){ \App\Controller\Backup::downloadTemplate(); });
    Flight::route('GET /backups/template/@date', function($date){ \App\Controller\Backup::downloadOldTemplate($date); });
    Flight::route('POST /backups/update', function(){ \App\Controller\Backup::submitFile(); });
    Flight::route('GET /backups/update/file/@date', function($date){ \App\Controller\Backup::updateDatabase($date); });
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