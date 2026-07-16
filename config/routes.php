<?php

declare(strict_types=1);

use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Admin\PaperAdminController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\DownloadController;
use App\Controllers\HomeController;
use App\Controllers\PaperController;
use App\Controllers\PaymentController;
use App\Core\Router;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/papers', [PaperController::class, 'index']);
$router->get('/papers/{slug}', [PaperController::class, 'show']);

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('/library', [PaymentController::class, 'library'], [AuthMiddleware::class]);
$router->post('/papers/{id}/checkout', [PaymentController::class, 'checkout'], [AuthMiddleware::class]);
$router->get('/payment/callback', [PaymentController::class, 'callback'], [AuthMiddleware::class]);
$router->get('/download/{id}', [DownloadController::class, 'download'], [AuthMiddleware::class]);

$router->get('/admin', [AdminDashboardController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/papers', [PaperAdminController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/papers/create', [PaperAdminController::class, 'create'], [AdminMiddleware::class]);
$router->post('/admin/papers', [PaperAdminController::class, 'store'], [AdminMiddleware::class]);
$router->get('/admin/papers/{id}/edit', [PaperAdminController::class, 'edit'], [AdminMiddleware::class]);
$router->post('/admin/papers/{id}', [PaperAdminController::class, 'update'], [AdminMiddleware::class]);
$router->post('/admin/papers/{id}/delete', [PaperAdminController::class, 'destroy'], [AdminMiddleware::class]);
