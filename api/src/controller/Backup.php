<?php

namespace App\Controller;

//-----> Classes
use App\Config;
use App\Util\Backup\Generator;
use App\Util\Backup\Reading;
use App\Util\Backup\Files;
use App\Model\Backup as BackupModel;

//-----> Dependencies
use Flight;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Backup{

    static function getAll()
    {
        $dir = BackupModel::getAllBackups();
        Flight::json([...$dir]);
        exit();
    }

    ########################################################################################################
    ################### UPDATE THE DATABASE WITH A BACKUP OF THE SERVER ####################################
    ########################################################################################################
    static function updateDatabase(string $date)
    {
        $filePath = Files::getFilePath($date);

        if(!$filePath){
            Config\Config::DefineError('#-003','El archivo indicado no existe en el servidor');
        }

        $response = BackupModel::updateDatabase($filePath);
        Flight::json($response);
        exit();
    }

    ############################################################################################
    ################### CREATE BACKUP WITH RECEIVED FILE #######################################
    ############################################################################################
    static function submitFile()
    {
        if(!isset($_FILES['file']) || $_FILES['file']['size'] == 0){
            Config\Config::DefineError('#-003', 'No se envio ningun archivo o su nombre de identificacion es distinta de "file"');
        }

        # Esta funcion, si se ejecuta correctamente, crea un backup en el servidor con la fecha actual
        self::createBackup($_FILES['file']);
        # Si no ocurre ningun error, la ejecucion continua y buscamos el nuevo backup en el servidor, para ejecutarlo en la base de datos
        self::updateDatabase(date('Y-m-d'));
    }
    
    # This function is responsible for creating and storing the backup.
    static function createBackup(array $file)
    {
        $temp_url = $file['tmp_name'];
    
        $spreadSheet = null;
        try{
            //-----> The received file is read and formatted by IOFactory
            $spreadSheet = IOFactory::load($temp_url);
        }
        catch(\Exception $e){
            # If the received file is not of Excel type, an error is generated, and we execute the following code
            //Config\Config::DefineError('#-003', 'El archivo recibido no es de tipo xlsx o el archivo esta corrompido');
            Config\Config::DefineError('#-003', $e->getMessage());
        }
        
        //-----> Read the different sheets of the file
        $providers = $spreadSheet->getSheet(0)->toArray();
        $equipments = $spreadSheet->getSheet(1)->toArray();
        $categories = $spreadSheet->getSheet(2)->toArray();
        
        //-----> Read the data from each sheet and create an INSERT INTO query for each table
        $providersQuery = Reading::createProvidersQuery($providers);
        $equipmentsQuery = Reading::createEquipmentsQuery($equipments);
        $categoriesQuery = Reading::createCategoriesQuery($categories);
        $provider_equipmentQuery = Reading::createProvider_EquipmentQuery($providers);

        $url_file = __DIR__."/../files/backups/".date('Y-m-d')."__Backup.sql";
        if(file_exists($url_file)){ unlink($url_file); }

        $file = fopen($url_file, 'w');

        fwrite($file, Config\Database::DROP_TABLES);
        fwrite($file, "\n\n");
        fwrite($file, Config\Database::CATEGORIES_STRUCTURE);
        fwrite($file, "\n\n");
        fwrite($file, $categoriesQuery);
        fwrite($file, "\n\n");
        fwrite($file, Config\Database::EQUIPMENTS_STRUCTURE);
        fwrite($file, "\n\n");
        fwrite($file, $equipmentsQuery);
        fwrite($file, "\n\n");
        fwrite($file, Config\Database::PROVIDERS_STRUCTURE);
        fwrite($file, "\n\n");
        fwrite($file, $providersQuery);
        fwrite($file, "\n\n");
        fwrite($file, Config\Database::PROVIDER_EQUIPMENT_STRUCTURE);
        fwrite($file, "\n\n");
        fwrite($file, $provider_equipmentQuery);
        fwrite($file, "\n\n");
        fwrite($file, Config\Database::RELATIONS_AND_CONFIGS);

        fclose($file);
    }

    ########################################################################################
    ################### CREATE AND DOWNLOAD TEMPLATE #######################################
    ########################################################################################
    static function downloadTemplate()
    {
        # Config teh file 
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                ->setCreator('ProveeMed')
                ->setTitle('Template para actualizar la base de datos');

        # Define the first page and mark it as active
        $activeSheet = $spreadsheet->getActiveSheet();
        # Give a name to the page
        $activeSheet->setTitle('Proveedores');
        
        $providers_data = BackupModel::getAllData(BackupModel::Providers_SQL);
        $equipmentsByProviders_data = BackupModel::getAllData(BackupModel::EquipmentsByProvider_SQL);

        # On that active page, create the providers' header and print the providers
        Generator::header($activeSheet,'provider');
        Generator::writeProviders($activeSheet, $providers_data, $equipmentsByProviders_data);
    
        # When a new page is created, it is automatically set as active
        # This allows us to avoid using the function $spreadsheet->getActiveSheet()
        $equipmentsSheet = $spreadsheet->createSheet(1);
        $equipmentsSheet->setTitle('Equipos');

        $equipments_data = BackupModel::getAllData(BackupModel::Equipments_SQL);

        # We send the parameter $equipmentsSheet to access and modify the second sheet
        # We create the header and the list of equipment on the second page
        Generator::header($equipmentsSheet, 'equipment');
        Generator::writeEquipments($equipmentsSheet, $equipments_data);
    
    
        $categoriesSheet = $spreadsheet->createSheet(2);
        $categoriesSheet->setTitle('Categorias');
    
        $categories_data = BackupModel::getAllData(BackupModel::Categories_SQL);

        Generator::header($categoriesSheet, 'category');
        Generator::writeCategories($categoriesSheet, $categories_data);

        //$writer = new Xlsx($spreadsheet);

        # These headers allow us to configure the file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ProveeMed.xlsx"');
        //header('Cache-Control: max-age=0');
    
        # This triggers the file download from the browser
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}