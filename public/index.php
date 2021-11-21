<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// serve static files since caddy container does not work as expected
$relativeFilePath = '.' . strtok($_SERVER['REQUEST_URI'], '?');
if (file_exists($relativeFilePath)) {
    $extMap = [
        '.js' => 'text/javascript',
        '.css' => 'text/css',
        '.eot' => 'application/vnd.ms-fontobject',
        '.svg' => 'image/svg+xml',
        '.ttf' => 'font/ttf',
        '.woff' => 'font/woff',
        '.png' => 'image/png',
        '.jpg' => 'image/jpg',
        '.ico' => 'image/x-icon',
        '.webmanifest' => 'application/manifest+json'
    ];
    foreach ($extMap as $ext => $headerValue) {
        if (strpos($_SERVER['REQUEST_URI'], $ext) > -1) {
            header('Content-Type: ' . $headerValue);
            echo file_get_contents($relativeFilePath);
            die();
        }
    }
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
