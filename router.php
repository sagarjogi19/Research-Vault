<?php

declare(strict_types=1);

/**
 * Router for PHP built-in server when document root is the project folder:
 * php -S localhost:8000 router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rawurldecode($uri);

if (preg_match('#^/(app|config|storage|database)(/|$)#i', $uri) || preg_match('#(^|/)\.#', $uri)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

$file = __DIR__ . $uri;
if ($uri !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
