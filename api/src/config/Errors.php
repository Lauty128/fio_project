<?php

namespace App\Config;

class Errors{

    const all = [
        // -------------------------------------------------------------------------------------
        // '#-000' => [
        //     'http_code'=> <integer>, // Indica el codigo http devuelto por el solicitud.
        //     'message'=> <string>, // MESSAGE Indica el error para los desarrolladores.
        // 
        //     'user_error_title' => [ // user_error_title  Indica el titulo del error que recibe el usuario.
        //         'es' => <string>
        //         'en' => <string>
        //     'user_error_message' => [ //user_error_message Indica el mensaje que recibe el usuario.
        //         'es' => <string> ,
        //         'en' => <string>
        //     ]
        // ]
        // --------------------------------------------------------------------------------------
        
        '#-404' => [
            'http_code'=> 404,
            'message'=>'El servidor no ha podido encontrar el endpoint solicitado.',
            'user_error_title' => [
                'es' => 'Endpoint inexistente',
                'en' => 'Non-existent endpoint'
            ],
            'user_error_message' => [
                'es' => 'El endpoint solicitado no existe en el servidor',
                'en' => 'The requested endpoint does not exist on the server',
            ]
        ],
    
        '#-401' => [
            'http_code'=> 401,
            'message'=>'No se encuentra autorizado para acceder a este contenido',
            'user_error_title' => [
                'es' => 'Autorización denegada',
                'en' => 'Access denied'
            ],
            'user_error_message' => [
                'es' => 'No cuenta con los permisos necesarios para acceder a este contenido',
                'en' => 'You do not have the necessary permissions to access this content',
            ]
        ],
        
        '#-000' => [
            'http_code'=> 500,
            'message'=>'Ocurrio un error durante la conexion a la base de datos (error code 500).',
            'user_error_title' => [
                'es' => 'Error en la base de datos',
                'en' => ''
            ],
            'user_error_message' => [
                'es' => 'Ocurrio un error durante la conexion a la base de datos',
                'en' => '',
            ]
        ],
        '#-001' => [
            'http_code'=> 500,
            'message'=>'Ocurrio un error durante la ejecucion de una consulta a la base de datos (error code 500).',
            'user_error_title' => [
                'es' => 'Error al consultar los datos',
                'en' => ''
            ],
            'user_error_message' => [
                'es' => 'Ocurrio un error durante la consulta a la base de datos',
                'en' => '',
            ]
        ],
        '#-002' => [
            'http_code'=> 404,
            'message'=> 'El servidor no pudo encontrar el elemento buscado',
            'user_error_title' => [
                'es' => 'No existe el elemento buscado',
                'en' => ''
            ],
            'user_error_message' => [
                'es' => 'La consulta fue procesada con exito pero no se logro encontrar el elemento',
                'en' => ''
            ]
        ],
        '#-003' => [
            'http_code'=> 404,
            'message'=> 'Error al enviar el archivo',
            'user_error_title' => [
                'es' => 'Error al enviar el archivo',
                'en' => ''
            ],
            'user_error_message' => [
                'es' => 'Ocurrio un error al enviar el archivo, por favor vuelva a enviarlo',
                'en' => ''
            ]
        ],
        '#-004' => [
            'http_code'=> 500,
            'message'=> 'Error el generar el backup. Revise que la sintaxis sea correcta',
            'user_error_title' => [
                'es' => 'Error al actualizar la base de datos',
                'en' => ''
            ],
            'user_error_message' => [
                'es' => 'Por favor, revise que la sintaxis del archivo sea correcta. Puede leer el manual para seguir los pasos',
                'en' => ''
            ]
        ],
    ];
    //con el codigo generado por paremetro obtenemos todo el mensaje de error
    /*Function getErrorMessage
     * Recibe como parametro el codigo generado y devuelve el mensaje de error completo.
     */
    static function getErrorMessage(string $code): Array | false
    {
        return self::all[$code] ?? false;
    }
}
