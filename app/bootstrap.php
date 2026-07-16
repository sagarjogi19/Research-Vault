<?php

declare(strict_types=1);

use App\Core\Autoloader;
use App\Core\Router;
use App\Core\Session;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Core/helpers.php';
require BASE_PATH . '/app/Core/Autoloader.php';

Autoloader::register(BASE_PATH);
loadEnv(BASE_PATH . '/.env');

date_default_timezone_set((string) config('app.timezone', 'UTC'));

Session::start();

$router = new Router();
require BASE_PATH . '/config/routes.php';

return $router;
