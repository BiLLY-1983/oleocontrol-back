<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Swagger UI Route
    |--------------------------------------------------------------------------
    |
    | Esta es la ruta donde se alojará la interfaz de Swagger UI. Puedes cambiarla
    | según tus necesidades, pero la ruta por defecto es 'api/documentation'.
    |
    */
    'swagger_ui_route' => 'api/documentation',

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir la versión de tu API. Esto se mostrará en la documentación.
    |
    */
    'api_version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | API Title
    |--------------------------------------------------------------------------
    |
    | Título de la API que aparecerá en la documentación de Swagger UI.
    |
    */
    'title' => 'API OleoControl',

    /*
    |--------------------------------------------------------------------------
    | API Description
    |--------------------------------------------------------------------------
    |
    | Descripción breve de lo que hace la API, que también se mostrará en la
    | interfaz de Swagger UI.
    |
    */
    'description' => 'Documentación de la API de la aplicación OleoControl',

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir la URL base de tu API. Por defecto se toma el valor
    | de la variable `APP_URL` en tu archivo .env.
    |
    */
    'base_url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Documentation Paths
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir las rutas a las cuales se les deben aplicar las
    | anotaciones Swagger. Este parámetro puede contener rutas absolutas o
    | relativas a tu proyecto.
    |
    */
    'documentation' => [
        'paths' => [
            'annotations' => base_path('app/Http/Controllers'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Settings
    |--------------------------------------------------------------------------
    |
    | Aquí puedes agregar configuraciones adicionales si lo necesitas, como
    | autenticación personalizada o la configuración de otros detalles de
    | la interfaz de Swagger UI.
    |
    */
];
