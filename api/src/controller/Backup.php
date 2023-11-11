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
        $filePath = Files::getBackupPath($date);

        if(!$filePath){
            Config\Config::DefineError('#-003','El archivo indicado no existe en el servidor');
        }

        $response = BackupModel::updateDatabase($filePath);
        Flight::json($response);
        exit();
    }

    ############################################################################################
    ################### DELETE BACKUP ##########################################################
    ############################################################################################
    static function deleteBackup(string $date){
        $backupPath = Files::getBackupPath($date);
        $templatePath = Files::getTemplatePath($date);

        if($backupPath){
            BackupModel::deleteBackup($backupPath, $templatePath);
            Flight::json([
                'status' => 200,
                'message' => 'El backup se elimino correctamente'
            ]);
        }
        else{
            Config\Config::DefineError('#-003','El archivo indicado no existe en el servidor');
        }
    }

    ############################################################################################
    ################### CREATE BACKUP WITH RECEIVED FILE #######################################
    ############################################################################################
    static function submitFile()
    {
        if(!isset($_FILES['file']) || $_FILES['file']['size'] == 0){
            Config\Config::DefineError('#-003', 'No se envio ningun archivo o su nombre de identificacion es distinta de "file"');
        }

        # If this function run correctly, creates a backup on server whit current date
        self::createBackup($_FILES['file']);
        self::storeTemplate($_FILES['file']);

        # If no have any error, the ejecution continues and search the new backup in the server, to ejecute this on DataBase
        self::updateDatabase(date('Y-m-d'));
    }

    static function storeTemplate(array $file){
        $temp_url = $file['tmp_name'];

        $url_file = __DIR__."/../files/templates/".date('Y-m-d')."__Template.xlsx";
        if(file_exists($url_file)){ unlink($url_file); }

        $template = fopen($url_file, 'w');

        fwrite($template, file_get_contents($temp_url));
        fclose($template);
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

        $backup = fopen($url_file, 'w');

        fwrite($backup, Config\Database::DROP_TABLES);
        fwrite($backup, "\n\n");
        fwrite($backup, Config\Database::CATEGORIES_STRUCTURE);
        fwrite($backup, "\n\n");
        fwrite($backup, $categoriesQuery);
        fwrite($backup, "\n\n");
        fwrite($backup, Config\Database::EQUIPMENTS_STRUCTURE);
        fwrite($backup, "\n\n");
        fwrite($backup, $equipmentsQuery);
        fwrite($backup, "\n\n");
        fwrite($backup, Config\Database::PROVIDERS_STRUCTURE);
        fwrite($backup, "\n\n");
        fwrite($backup, $providersQuery);
        fwrite($backup, "\n\n");
        fwrite($backup, Config\Database::PROVIDER_EQUIPMENT_STRUCTURE);
        fwrite($backup, "\n\n");
        fwrite($backup, $provider_equipmentQuery);
        fwrite($backup, "\n\n");
        fwrite($backup, Config\Database::RELATIONS_AND_CONFIGS);

        fclose($backup);
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

    static function downloadOldTemplate(string $date)
    {
        $template = Files::getTemplatePath($date);  
        if(!$template){
            Config\Config::DefineError('#-003','El archivo indicado no existe en el servidor');
            exit();
        }

        $name = $template['filename'];
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=$name");

        readfile($template['path']);
        exit();
    }
}