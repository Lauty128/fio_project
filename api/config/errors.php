<?php

define("ERROR_HANDLER", [
    '#-404' => [
        'http_code'=> 404,
        'message'=>'The server has not found the endpoint of the requested',
        'user_error_title' => [
            'es' => 'Endpoint inexistente',
            'en' => 'Nonexistent endpoint'
        ],
        'user_error_message' => [
            'es' => 'El endpoint solicitado no existe en el servidor',
            'en' => 'The requested endpoint does not exist on the server',
        ]
    ],
    '#-401' => [
        'http_code'=> 401,
        'message'=>'You are not authorized to access this content',
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
        'message'=>'An error occurred during the database connection (error code 500).',
        'user_error_title' => [
            'es' => 'Error en la base de datos',
            'en' => 'Database error'
        ],
        'user_error_message' => [
            'es' => 'Ocurrio un error durante la conexion a la base de datos',
            'en' => 'An error occurred during the database connection',
        ]
    ],
    '#-001' => [
        'http_code'=> 500,
        'message'=>'An error occurred during the execution of a database query (error code 500).',
        'user_error_title' => [
            'es' => 'Error al consultar los datos',
            'en' => 'Error while querying the data'
        ],
        'user_error_message' => [
            'es' => 'Ocurrio un error durante la consulta a la base de datos',
            'en' => 'An error occurred during the database query',
        ]
    ],
    '#-002' => [
        'http_code'=> 404,
        'message'=> 'The server has not found the requested element ',
        'user_error_title' => [
            'es' => 'No existe el elemento buscado',
            'en' => 'The request element has not be founded'
        ],
        'user_error_message' => [
            'es' => 'La consulta fue procesada con exito pero no se logro encontrar el elemento',
            'en' => 'The query was processed successfully but the element could not be found.'
        ]
    ]
]);