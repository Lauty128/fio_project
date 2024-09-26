<?php

namespace App\Model;

use App\Config;
use App\Config\Database;
use PDO, PDOException;

#----- Conexion a base de datos
Config\Database::connect();

class Backup{

    const Providers_SQL = "SELECT * 
                FROM users
                WHERE user_type_id = 3
                ORDER BY id ASC";

    const Equipments_SQL = "SELECT * 
                FROM equipments 
                ORDER BY id ASC";

    const Categories_SQL = "SELECT * 
                FROM categories
                ORDER BY id ASC";

    const EquipmentsByProvider_SQL = "SELECT pe.provider_id, GROUP_CONCAT(pe.equipment_id) AS equipments 
                FROM provider_equipments pe 
                GROUP BY pe.provider_id 
                ORDER BY pe.provider_id";


    static function getAllBackups(){
        $dir = scandir(__DIR__."/../files/backups");
        unset($dir[0],$dir[1]); // Remueve los primeros archivos del directorio | [0] => '.', [1] => '..' 
        array_pop($dir);  // Elimina el ultimo archivo. Este archivo se llama "main.txt", este indica el main back up.
        
        // Recorro el array al reves para enviar los archivos ordenados por fecha. 
        return array_reverse($dir);
    }

    static function deleteBackup(array $backup, array | bool $template){
        unlink($backup['path']);
        if($template){
            unlink($template['path']);
        }
    }

    static function updateDatabase(array $file)
    {
        $sql = file_get_contents($file['path']);
        
        # Una transacccion no es necesaria aqui, ya que solo se ejecuta una consulta.
        $PDO = Database::$connection;
        //$PDO->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        //$PDO->beginTransaction();
        try{
            $PDO->exec($sql);
            //$PDO->commit();
            return [
                'code' => 200,
                'message' => 'Base de datos actualizada correctamente'
            ];
        }
        catch(PDOException $error){
            # BORRA el archivo SQL del servidor si tiene algun error
            # (Deberia optimizarlo para que solo lo elimine si hay un error de sintaxis\\\\\\\\\\\\\\\\\\\\\\\\\\\
            # Puede haber un error en la conexion, pero la sintaxis estar bien)
            unlink($file['path']);
            $template_url = __DIR__.'/../files/templates/'.date('Y-m-d').'__Template.xlsx';
            if(file_exists($template_url)){
                unlink($template_url);
            }
            //$PDO->rollBack();
            Config\Config::DefineError('#-001', $error->getMessage());
        }
    }

    //-----------------> FUNCTION FOR GETTING DATA
    static function getAllData(string $SQL)
    {
        try{
            # Preparamos la consulta con el generador string
            $query = Config\Database::$connection->prepare($SQL);

            # Ejecutamos la consulta
            $query->execute();
            # Obtenemos un arreglo con los datos recibidos 
            $data = $query->fetchAll(PDO::FETCH_ASSOC);

            # Devuelve el valor para usarlo en providers.model.php
            return $data;
        }
        catch(PDOException $error){
            Config\Config::DefineError('#-001', $error->getMessage());
        } 
    }

}