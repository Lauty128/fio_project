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
        $main = Files::getMainBackup();
        Flight::json([
            'main' => $main,
            'files' => [...$dir]
        ]);
        exit();
    }

    #################################################################################################################
    ################### ACTUALIZACION DE LA BASE DE DATOS CON EL BACKUP INDICADO ####################################
    #################################################################################################################
    static function updateDatabase(string $date)
    {
        $filePath = Files::getBackupPath($date);

        if(!$filePath){
            Config\Config::DefineError('#-003','El archivo indicado no existe en el servidor');
        }

        // EJECUCION DEL BACKUP EN LA BASE DE DATOS
        $response = BackupModel::updateDatabase($filePath);

        // SI EL BACKUPS ES ACTUALIZADO EXITOSAMENTE, ENTOCES EL MAIN.TXT ES MODIFICADO POR LA VARIABLE $DATE
        Files::changeMainBackup($date);

        // RETORNAR MENSAJE
        Flight::json($response);
        exit();
    }

    ############################################################################################
    ################### BORRADO DE BACKUP ######################################################
    ############################################################################################
    static function deleteBackup(string $date){
        $backupPath = Files::getBackupPath($date);
        $templatePath = Files::getTemplatePath($date);

        if($backupPath){
            BackupModel::deleteBackup($backupPath, $templatePath);
            Flight::json([
                'code' => 200,
                'message' => 'El backup se elimino correctamente'
            ]);
        }
        else{
            Config\Config::DefineError('#-003','El archivo indicado no existe en el servidor');
        }
    }

    ############################################################################################
    ################### CREACION DEL BACKUP CON ARCHIVO XLSX ###################################
    ############################################################################################
    static function submitFile()
    {
        # Se verifica la existenia de un archivo enviado y que sea mayor a 0 bytes
        if(!isset($_FILES['file']) || $_FILES['file']['size'] == 0){
            Config\Config::DefineError('#-003', 'No se envio ningun archivo o su nombre de identificacion es distinta de "file"');
        }

        # Si la funcion no se corta, se crea un backup en el servidor con la fecha actual
        self::createBackup($_FILES['file']);
        # Y con esta funcion almacenamos el archivo xlsx, para tener el modelo con el que se creo el backup
        self::storeTemplate($_FILES['file']);

        # Si no hay ningun error, la ejecucion continua y se busca el nuevo backup en el servidor para ejecutarlo en la base de datos
        self::updateDatabase(date('Y-m-d'));
    }

    ############################################################################################
    ################### ALMACENAR TEMPLATE EN EL SERVIDOR ######################################
    ############################################################################################
    static function storeTemplate(array $file){
        # Se obtiene la url temporal del archivo leido
        $temp_url = $file['tmp_name'];

        # Se crea la URL con el archivo temporal obtenido y le asignamos el nombre de la fecha con la extension "__Template.xlsx" 
        $url_file = __DIR__."/../files/templates/".date('Y-m-d')."__Template.xlsx";
        if(file_exists($url_file)){ unlink($url_file); } # Si existe este archivo se elimina (Osea si ese mismo dia ya se habia creado uno)

        # Abrimos el archivo creado con la flag "w" y le asignamos el contenido archivo temporal
        $template = fopen($url_file, 'w');
        fwrite($template, file_get_contents($temp_url));
        fclose($template);
    }
    
    ############################################################################################
    ################### CREAR Y ALMACENAR BACKUP ###############################################
    ############################################################################################
    static function createBackup(array $file)
    {
        $temp_url = $file['tmp_name'];
    
        $spreadSheet = null;
        try{
            //-----> El archivo recibido es leido por IOFactory
            $spreadSheet = IOFactory::load($temp_url);
        }
        catch(\Exception $e){
            # Si el archivo recibido no es del tipo Excel, generamo un error y ejecutamos el siguiente código
            //Config\Config::DefineError('#-003', 'El archivo recibido no es de tipo xlsx o el archivo esta corrompido');
            Config\Config::DefineError('#-003', $e->getMessage());
        }
        
        //----->Leer las diferentes hojas del expediente.
        $providers = $spreadSheet->getSheet(0)->toArray();
        $equipments = $spreadSheet->getSheet(1)->toArray();
        $categories = $spreadSheet->getSheet(2)->toArray();
        
        //-----> Lee los datos de cada hoja y crea una consulta INSERT INTO para cada tabla
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
    ################### CREA Y DESCARGA EL TEMPLATE #######################################
    ########################################################################################
    static function downloadTemplate()
    {
        # Configurar el archivo 
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                ->setCreator('ProveeMed')
                ->setTitle('Template para actualizar la base de datos');

        # Define la primera pagina y marca si esta activa
        $activeSheet = $spreadsheet->getActiveSheet();
        # Se indica el nombre a la pagina
        $activeSheet->setTitle('Proveedores');
        
        $providers_data = BackupModel::getAllData(BackupModel::Providers_SQL);
        $equipmentsByProviders_data = BackupModel::getAllData(BackupModel::EquipmentsByProvider_SQL);

        # En esa pagina activa, crea el encabezado de los proveedores e imprime los proveedores
        Generator::header($activeSheet,'provider');
        Generator::writeProviders($activeSheet, $providers_data, $equipmentsByProviders_data);
    
        #Cuando la nueva pagina es creada, es seteada actomaticamente como selectiva
        # Esto nos permite evitar el uso de la funcion $spreadsheet->getActiveSheet()
        $equipmentsSheet = $spreadsheet->createSheet(1);
        $equipmentsSheet->setTitle('Equipos');

        $equipments_data = BackupModel::getAllData(BackupModel::Equipments_SQL);

        # Enviamos el parametro $equipmentsSheet para acceder y modificar la siguienteaCreamoes el encabezxjoho a
        # We create the header and the list of equipment on the second page
        Generator::header($equipmentsSheet, 'equipment');
        Generator::writeEquipments($equipmentsSheet, $equipments_data);
    
    
        $categoriesSheet = $spreadsheet->createSheet(2);
        $categoriesSheet->setTitle('Categorias');
    
        $categories_data = BackupModel::getAllData(BackupModel::Categories_SQL);

        Generator::header($categoriesSheet, 'category');
        Generator::writeCategories($categoriesSheet, $categories_data);

        //$writer = new Xlsx($spreadsheet);

        # Estas cabeceras permiten configurar la descarga  del archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ProveeMed.xlsx"');
        //header('Cache-Control: max-age=0');
    
        # Esto activa la descarga del archivo desde el navegador.
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    ##############################################################################################
    ################### DESCARGAR UN TEMPLATE DEL SERVIDOR #######################################
    ##############################################################################################
    static function downloadOldTemplate(string $date)
    {
        # Se obtiene el template con el $date indicado
        $template = Files::getTemplatePath($date);  
        if(!$template){
            # Si no lo encuentra se corta la funcion y se retorna un error
            Config\Config::DefineError('#-003','El archivo indicado no existe en el servidor');
            exit();
        }

        # Se almacena el nombre del template y se configuran las cabeceras para descargarlo
        $name = $template['filename'];
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=$name");

        # Se lee el contenido del archivo para ejecutar la descarga
        readfile($template['path']);
        exit();
    }
}