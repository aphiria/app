<?php

declare(strict_types=1);

$requestUri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($requestUri !== '/' && file_exists(__DIR__ . "/public$requestUri")) {
    return false;
}

require_once __DIR__ . '/public/index.php';
