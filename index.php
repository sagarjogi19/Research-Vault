<?php

declare(strict_types=1);

/**
 * Front controller for hosts that use the project root as the document root.
 */
define('WEB_ROOT_MODE', 'project');

$router = require __DIR__ . '/app/bootstrap.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
