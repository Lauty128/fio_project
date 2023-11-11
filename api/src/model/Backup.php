<?php

namespace App\Model;

use App\Config;
use App\Config\Database;
use PDO, PDOException;

#----- DataBase Connection
Config\Database::connect();

class Backup{

    const Providers_SQL = "SELECT * 
                FROM provider 
                ORDER BY providerID ASC";

    const Equipments_SQL = "SELECT * 
                FROM equipment 
                ORDER BY equipmentID ASC";

    const Categories_SQL = "SELECT * 
                FROM category
                ORDER BY categoryID";

    const EquipmentsByProvider_SQL = "SELECT pe.providerID, GROUP_CONCAT(pe.equipmentID) AS equipments 
                FROM provider_equipment pe 
                GROUP BY pe.providerID 
                ORDER BY pe.providerID";


    static function getAllBackups(){
        $dir = scandir(__DIR__."/../files/backups");
        unset($dir[0],$dir[1]); // This removes the firsts files of the directory. | [0] => '.', [1] => '..'
        array_pop($dir);    // This removes the latest file. This file is called "main.txt", wich indicates the main backup
        
        // The array is reversed for sending files ordered by date.
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
        
        # A transaction is not necessary here, since only one query is executed.
        # Although the query is very large, it is still only one and the transaction is automatically activated for that single transaction
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
            # DELETE THE SQL FILE FROM THE SERVER AS IT HAS SOMETHING WORNG
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
            # We prepare the query with the generated string
            $query = Config\Database::$connection->prepare($SQL);

            # We execute the query
            $query->execute();
            # We obtain an array with the received data
            $data = $query->fetchAll(PDO::FETCH_ASSOC);

            # We return the value to use it in providers.model.php
            return $data;
        }
        catch(PDOException $error){
            Config\Config::DefineError('#-001', $error->getMessage());
        } 
    }

}