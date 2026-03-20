<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Router.php';

spl_autoload_register(static function (string $class): void {
    $folders = [__DIR__ . '/../controllers/', __DIR__ . '/../models/'];
    foreach ($folders as $folder) {
        $file = $folder . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$router = new Router();

$router->get('/', [DashboardController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/experts', [ExpertController::class, 'index']);
$router->get('/experts/create', [ExpertController::class, 'create']);
$router->post('/experts/store', [ExpertController::class, 'store']);
$router->get('/experts/show', [ExpertController::class, 'show']);
$router->get('/experts/edit', [ExpertController::class, 'edit']);
$router->post('/experts/update', [ExpertController::class, 'update']);
$router->post('/experts/delete', [ExpertController::class, 'delete']);
$router->post('/experts/approve', [ExpertController::class, 'approve']);
$router->get('/experts/export', [ExpertController::class, 'exportPdf']);
$router->get('/api/skills/search', [ApiController::class, 'searchSkills']);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
