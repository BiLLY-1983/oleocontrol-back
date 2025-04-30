<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar los ajustes para el intercambio de recursos entre
    | orígenes cruzados o "CORS". Esto determina qué operaciones de origen cruzado
    | pueden ejecutarse en los navegadores web. Puedes ajustar estos valores según
    | sea necesario.
    |
    | Para aprender más: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],  // Permite todos los métodos HTTP (GET, POST, PUT, etc.)

    'allowed_origins' => [
        '*', 
    ],

    'allowed_origins_patterns' => [],  // No se necesita ningún patrón específico

    'allowed_headers' => ['*'],  // Permite todas las cabeceras (como 'Authorization', 'Content-Type', etc.)

    'exposed_headers' => [],  // No se necesitan cabeceras expuestas adicionales

    'max_age' => 0,  // No se necesita cachear el preflight

    'supports_credentials' => false,  // Se necesitan credenciales para las solicitudes

];
