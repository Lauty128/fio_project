<?php

namespace App\Util\Backup;

class Reading{

    const HEADER__PROVIDER = "INSERT INTO `provider` (`providerID`, `name`, `mail`, `web`, `address`, `phone`) VALUES \n";
    const HEADER__EQUIPMENT = "INSERT INTO `equipment` (`equipmentID`, `name`, `categoryID`, `umdns`, `description`, `price`, `specifications`) VALUES \n";
    const HEADER__CATEGORY = "INSERT INTO `category` (`categoryID`, `name`) VALUES \n";
    const HEADER__PROVIDER_EQUIPMENT = "INSERT INTO `provider_equipment` (`providerID`, `equipmentID`) VALUES \n";
  
    static function createCategoriesQuery(array $array){
      $Query = self::HEADER__CATEGORY;
        for ($i=1; $i < count($array); $i++) { 
                $Query .= "(".$array[$i][0].", '".$array[$i][1]."'),\n";
        }
  
      return substr($Query,0, (strlen($Query) - 2)).";";
    }
  
    static function createProvidersQuery(array $array){
      $Query = self::HEADER__PROVIDER;
        for ($i=1; $i < count($array); $i++) { 
          $mail = (!$array[$i][2] || $array[$i][2] == 'N\A') ? 'NULL' : "'".$array[$i][2]."'";
          $web = (!$array[$i][3] || $array[$i][3] == 'N\A') ? 'NULL' : "'".$array[$i][3]."'";
          $address = (!$array[$i][4] || $array[$i][4] == 'N\A') ? 'NULL' : "'".$array[$i][4]."'";
          $phone = (!$array[$i][5] || $array[$i][5] == 'N\A') ? 'NULL' : "'".$array[$i][5]."'";
            
          $Query .= "(".$array[$i][0].",\"".$array[$i][1]."\",$mail,$web,$address, $phone),\n";
        }
  
      return substr($Query,0, (strlen($Query) - 2)).";";
    }
  
    static function createEquipmentsQuery(array $array){
      $Query = self::HEADER__EQUIPMENT;
        for ($i=1; $i < count($array); $i++) { 
          $price = $array[$i][5] ?? 'NULL';
          $Query .= "(".$array[$i][0].", '".$array[$i][1]."', ".$array[$i][2].", '".$array[$i][3]."', '".$array[$i][4]."', ".$price.", NULL),\n";
        }
  
      return substr($Query,0, (strlen($Query) - 2)).";";
    }
  
    static function createProvider_EquipmentQuery(array $array){
      $Query = self::HEADER__PROVIDER_EQUIPMENT;
      for ($i=1; $i < count($array); $i++) { 
        $equipments = explode(',', $array[$i][6]);
        $providerID = $array[$i][0];
        
        foreach ($equipments as $equipment) {
          $Query .= "(".$providerID.", ".$equipment."),\n";  
        }
      }
  
      return substr($Query,0, (strlen($Query) - 2)).";";
    }
  }