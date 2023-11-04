<?php

    namespace App\Config;
    use Flight;

    class Config{

        //--------------------- Properties -----------------------------
        //------> Validation
        const ACCESS_TOKEN = '<token>';
        
        //------> Database
        const DB_SERVER = 'localhost';
        const DB_NAME = 'fio_project';
        const DB_USER = 'root';
        const DB_PASSWORD = '';

        //------> Default Values
        const PAGE = 0;
        const LIMIT = 40;
        const SMALL_LIMIT = 10;

        //------> Order 
        const ORDER = 'default';
        const ORDER_TYPES = ['N-ASC','N-DESC','ID-ASC','ID-DESC'];
        
        //------> Variables for backups
        const HEADER_STYLE = [
            'font' => [
                'bold' => true,
                'color' => [
                    'argb' => 'FFFFFF'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'argb' => 'FFFFFF'
                    ]
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '044c84',
                ]
            ]
        ];
        
        const HEADER_FIELDS = [
            'provider' => [
                'ID',
                ['value' => 'Nombre', 'width' => 40 ],
                ['value' => 'Mail', 'width' => 40 ],
                ['value' => 'Sitio web', 'width' => 50 ],
                ['value' => 'Dirección/es', 'width' => 60 ],
                ['value' => 'Teléfono/s', 'width' => 40 ],
                ['value' => 'Equipos', 'width' => 80 ]
            ],
            'equipment' => [
                'ID',
                ['value' => 'Nombre', 'width' => 40 ],
                ['value' => 'Categoría', 'width' => 16 ],
                ['value' => 'UMDNS', 'width' => 15 ],
                ['value' => 'Descripción', 'width' => 50 ],
                ['value' => 'Precio', 'width' => 17 ],
            ],
            'category' => [
                'ID',
                ['value' => 'Nombre', 'width' => 30 ]
            ]
        ];

        //--------------------- Methods -----------------------------
        //------> Error handler
        static function DefineError($code, $errorMessage = 'Error'){
            // Define response to return
            $code_response = \App\Config\Errors::getErrorMessage($code);

            $response = [
                ...$code_response,
                'error_message' => $errorMessage
            ] ?? false;

            Flight::json($response, $response['http_code'] ?? 500);
            exit();
        }
    }

