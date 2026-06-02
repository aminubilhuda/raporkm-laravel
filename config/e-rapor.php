<?php

return [
    'name' => env('APP_NAME', 'E-Rapor KM'),
    'session_timeout' => env('SESSION_TIMEOUT', 7200),
    'pwa_token_lifetime' => env('PWA_TOKEN_LIFETIME', 365 * 24 * 60),
    'remember_token_lifetime' => env('REMEMBER_TOKEN_LIFETIME', 30),
];
