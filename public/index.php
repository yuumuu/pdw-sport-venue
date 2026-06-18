<?php

require_once __DIR__ . '/../src/helpers/helper.php';
require_once __DIR__ . '/../src/helpers/storage.php';
require_once __DIR__ . '/../src/config/database.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/controllers/',
        __DIR__ . '/../src/models/',
        __DIR__ . '/../src/core/',
        __DIR__ . '/../src/config/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($basePath && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$url = trim($path, '/');
if (empty($url)) {
    $url = 'home';
}

Router::handle($url);
