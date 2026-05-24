<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration CORS pour CAMWATER PRO API.
    | Permet aux applications frontend (React, Vue, Angular) d'accéder à l'API.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // les domaines autorisés
    'allowed_origins' => ['https://app.camwater.cm', 'https://admin.camwater.cm'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],

    'max_age' => 86400, // 24 heures

    'supports_credentials' => true,

];
