<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    // Itt ne használj csillagot (*), írd ki pontosan a Nuxt címed!
    'allowed_origins' => ['http://localhost:3000'], 

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // EZT ÁLLÍTSD TRUE-RA! Enélkül a süti (session) sosem fog átmenni.
    'supports_credentials' => true,
];
