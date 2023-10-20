<?php
// #-001 message for internal server error.
define("ERROR_HANDLER", [
    '#-404' => [
        // INDICA EL CODIGO HTTP DEVUELTO POR LA REQUEST
        'http_code'=> 404,
        // MENSAJE INDICA EL ERROR QUE VA A LEER EL DESARROLLADOR
        'message'=>'No existe el endpoint solicitado en el servidor.',
        'user_error_title' => [
            'es' => 'Endpoint inexistente',
            'en' => ''
        ],
        'user_error_message' => [
            'es' => 'El endpoint solicitado no existe en el servidor',
            'en' => '',
        ]
    ],

    '#-401' => [
        // INDICA EL CODIGO HTTP DEVUELTO POR LA REQUEST
        'http_code'=> 401,
        // MENSAJE INDICA EL ERROR QUE VA A LEER EL DESARROLLADOR
        'message'=>'No se encuentra autorizado para acceder a este contenido',
        // user_error_title INDICA EL TITULO DEL ERROR QUE VA A LEER EL USUARIO
        'user_error_title' => [
            'es' => 'Autorización denegada',
            'en' => ''
        ],
        'user_error_message' => [
            'es' => 'No cuenta con los permisos necesarios para acceder a este contenido',
            'en' => '',
        ]
    ],
    
    '#-000' => [
        // INDICA EL CODIGO HTTP DEVUELTO POR LA REQUEST
        'http_code'=> 500,
        // MENSAJE INDICA EL ERROR QUE VA A LEER EL DESARROLLADOR
        'message'=>'Ocurrio un error durante la conexion a la base de datos (error code 500).',
        // user_error_title INDICA EL TITULO DEL ERROR QUE VA A LEER EL USUARIO
        'user_error_title' => [
            'es' => 'Error en la base de datos',
            'en' => ''
        ],
        // user_error_message INDICA EL MENSAJE DEL ERROR QUE VA A LEER EL USUARIO
        'user_error_message' => [
            'es' => 'Ocurrio un error durante la conexion a la base de datos',
            'en' => '',
        ]
    ],
    '#-001' => [
        // INDICA EL CODIGO HTTP DEVUELTO POR LA REQUEST
        'http_code'=> 500,
        // MENSAJE INDICA EL ERROR QUE VA A LEER EL DESARROLLADOR
        'message'=>'Ocurrio un error durante la ejecucion de una consulta a la base de datos (error code 500).',
        // user_error_message INDICA EL MENSAJE DEL ERROR QUE VA A LEER EL USUARIO
        // user_error_title INDICA EL TITULO DEL ERROR QUE VA A LEER EL USUARIO
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
    ]







    // '#-002' => [
    //     'http_code'=> 422,
    //     'message'=> 'FILTROS QUE NO COMPRENDE EL SERVIDOR O ACCIONES RARAS/INESPERADAS',
    //     'user_error_message' => [
    //         'es' => 'Ocurrio un error durante la consulta a la base de datos',
    //         'en' => ''
    //     ],
    //     'user_error_title' => [
    //         'es' => 'Error al consultar los datos',
    //         'en' => ''
    //     ]
    // ]
]);