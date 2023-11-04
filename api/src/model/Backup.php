<?php

namespace App\Model;

use App\Config;
use PDO, PDOException;

#----- DataBase Connection
Config\Database::connect();

class Backup{

    //-----------------> FUNCTION FOR GETTING DATA
    static function getAllProviders()
    {
        $sql = "SELECT * 
        FROM provider 
        ORDER BY providerID ASC";

        try{
            # We prepare the query with the generated string
            $query = Config\Database::$connection->prepare($sql);

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

    static function getAllEquipments()
    {
        $sql = "SELECT * 
        FROM equipment 
        ORDER BY equipmentID ASC";
        
        try{
            # We prepare the query with the generated string
            $query = Config\Database::$connection->prepare($sql);

            # We execute the query
            $query->execute();
            # Obtenemos un array con los datos recibidos
            $data = $query->fetchAll(PDO::FETCH_ASSOC);

            # Retornamos el valor para usarlo en proveedores.model.php
            return $data;
        }
        catch(PDOException $error){
            Config\Config::DefineError('#-001', $error->getMessage());
        } 
    }

    static function getAllCategories()
    {
        $sql = "SELECT * 
        FROM category
        ORDER BY categoryID";

        try{
            # Preparamos la query con el string generado
            $query = Config\Database::$connection->prepare($sql);

            # Ejecutamos  la cnsulta
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

    static function getAllEquipmentsByProvider()
    {
        $sql = "SELECT pe.providerID, GROUP_CONCAT(pe.equipmentID) AS equipments 
        FROM provider_equipment pe 
        GROUP BY pe.providerID 
        ORDER BY pe.providerID";

        try{
            # We prepare the query with the generated string
            $query = Config\Database::$connection->prepare($sql);

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