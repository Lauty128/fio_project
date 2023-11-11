<?php

namespace App\Util\Backup;

use Exception;

class Files{

    static function getBackupPath(string $date):array | bool
    {
        $filename = $date."__Backup.sql";
        $path = __DIR__."/../../files/backups/".$filename;
        
        if(!file_exists($path)){ return false; }
        
        return [
            'path' => $path,
            'filename' => $filename
        ];
    }

    static function getTemplatePath(string $date):array | bool
    {
        $filename = $date."__Template.xlsx";
        $path = __DIR__."/../../files/templates/".$filename;
        
        if(!file_exists($path)){ return false; }
        
        return [
            'path' => $path,
            'filename' => $filename
        ];
    }

    static function getMainBackup()
    {
        $mainPath = __DIR__."/../../files/backups/main";
        $mainBackup = file_get_contents($mainPath);

        return $mainBackup;
    }

    static function changeMainBackup($date):bool
    {
        try{
            $mainPath = __DIR__."/../../files/backups/main";
            $file = fopen($mainPath, 'w+');

            fwrite($file, $date);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
}