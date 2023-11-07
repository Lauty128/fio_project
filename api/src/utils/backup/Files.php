<?php

namespace App\Util\Backup;

class Files{

    static function getFilePath(string $date):array | bool
    {
        $filename = $date."__Backup.sql";
        $path = __DIR__."/../../files/backups/".$filename;
        
        if(!file_exists($path)){ return false; }
        
        return [
            'path' => $path,
            'filename' => $filename
        ];
    }

}