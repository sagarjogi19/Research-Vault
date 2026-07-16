<?php

declare(strict_types=1);

define('WEB_ROOT_MODE', 'public');

$router = require dirname(__DIR__) . '/app/bootstrap.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
