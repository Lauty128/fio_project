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
        unset($dir[0],$dir[1]);
        return $dir;
        # The following code can be used to return false if there are no backups
        // if(count($dir) > 2){
        //     unset($dir[0],$dir[1]);
        //     return $dir;
        // }
        // else{
        //     return false;
        // }
    }

    static function updateDatabase(array $file)
    {
        $sql = file_get_contents($file['path']);
        
        # Aqui no es necesario una transaccion, ya que solo se ejecuta una consulta.
        # Aunque es muy grande la consulta, sigue siendo una sola y la transaccion se activa automaticamente para esa sola transaccion
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
            ## ELIMINAR EL ARCHIVO SQL DEL SERVIDOR, YA QUE TIENE ALGO MAL
            # Deberia optimizarlo para que solo lo elimine si hay un error de sintaxis
            # Puede haber un error en la conexion, pero la sintaxis estar bien
            unlink($file['path']);
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