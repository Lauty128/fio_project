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

}