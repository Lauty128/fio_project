<?php

namespace App\Util\Backup;

//----- Class
use App\Config;

//----- Dependencies
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class Generator{

    /*
     *   Make a header on the workpage sended as parameter. 
     * 
    */
    static function header(Worksheet $workSheet, string $table):void
    {
        # This help us to add rows of dynamic form.
        $columns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','O'];
        
        # If the table isn't 'provider', or 'equipment', then we will end the function.
        if(!isset(Config\Config::HEADER_FIELDS[$table])) return; 

        # Field, of row of 1, at height of 20
        $workSheet->getRowDimension(1)
            ->setRowHeight(20);

        # This goes from A1 to the last column on header (always in row 1)
        $workSheet->getStyle('A1:'.($columns[(count(Config\Config::HEADER_FIELDS[$table]) - 1)]).'1')
            ->applyFromArray(Config\Config::HEADER_STYLE);

        # We read one of the two arrays, inside of 'HEADER_FIELDS' array.
        foreach (Config\Config::HEADER_FIELDS[$table] as $key => $field){

            # If the element is type String or Array, but it doesn´t have a custom width, we set this value in 10
            $width = $field['width'] ?? 10;

            # We set the width of that column, specified or by default.
            $workSheet->getColumnDimension($columns[$key])
                        ->setWidth((float)$width);

            # Using a format 'A1', 'B1', to represent a field, we give values to each one of them.
            # The '1' remains fixed, because it's the first row, only change the letters, with help of '$columns' array.
            $workSheet->setCellValue(
                $columns[$key].'1', 
                # If the element type is string, use this, else use 'value' atribute.
                (is_string($field)) ? $field : $field['value']
            );
        }
    }

    /**
     * Read DataBase and writes on workpage the list of providers, whit her data. 
     * Starts from the second (2nd) row onwards. 
    */
    static function writeProviders(Worksheet $workSheet, $providers, $equipments)
    {
         # We align (horizontally) all of fields on the left. 
         $workSheet->getStyle('A1:F'.(count($providers) + 1))
         ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        for ($i=2; $i <= (count($providers) + 1); $i++)
        {     
            $workSheet->getRowDimension($i)
            ->setRowHeight(20);

            $workSheet->setCellValue('A'.$i, $providers[$i-2]['providerID']);
            $workSheet->setCellValue('B'.$i, $providers[$i-2]['name']);
            $workSheet->setCellValue('C'.$i, $providers[$i-2]['mail']);
            $workSheet->setCellValue('D'.$i, $providers[$i-2]['web']);
            $workSheet->setCellValue('E'.$i, $providers[$i-2]['address']);
            $workSheet->setCellValue('F'.$i, $providers[$i-2]['phone']);
            $workSheet->setCellValue('G'.$i, $equipments[$i-2]['equipments']);
        }

    }

    /**
     * Read DataBase and writes on workpage the list of equipments, whit her data.
     * Starts from the second (2nd) row onwards. 
    */
    static function writeEquipments(Worksheet $workSheet, $equipments)
    {
        # We align (horizontally) all of fields on the left. 
        $workSheet->getStyle('A1:G'.(count($equipments) + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        for ($i=2; $i <= (count($equipments) + 1); $i++)
        { 
            $workSheet->getRowDimension($i)
            ->setRowHeight(20);

            $workSheet->setCellValue('A'.$i, $equipments[$i-2]['equipmentID']);
            $workSheet->setCellValue('B'.$i, $equipments[$i-2]['name']);
            $workSheet->setCellValue('C'.$i, $equipments[$i-2]['categoryID']);
            $workSheet->setCellValue('D'.$i, $equipments[$i-2]['umdns']);
            $workSheet->setCellValue('E'.$i, $equipments[$i-2]['description']);
            $workSheet->setCellValue('F'.$i, $equipments[$i-2]['price']);
        }

    }

    static function writeCategories(Worksheet $workSheet, $categories)
    {
        # We align (horizontally) all of fields on the left. 
        $workSheet->getStyle('A1:B'.(count($categories) + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        for ($i=2; $i <= (count($categories) + 1); $i++)
        { 
            $workSheet->getRowDimension($i)
            ->setRowHeight(20);

            $workSheet->setCellValue('A'.$i, $categories[$i-2]['categoryID']);
            $workSheet->setCellValue('B'.$i, $categories[$i-2]['name']);
        }

    }
}