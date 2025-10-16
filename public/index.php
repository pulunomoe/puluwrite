<?php

session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

use App\Controller\FileController;
use App\Controller\FolderController;
use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

function debug(mixed $value): void
{
    header('Content-Type: application/json');
    echo json_encode($value);
    exit;
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
$container = $containerBuilder->build();

$app = Bridge::create($container);
$app->addRoutingMiddleware();
$app->addErrorMiddleware($_ENV['DEBUG'], true, true);
$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));

$auth = new AuthMiddleware($container->get(ResponseFactoryInterface::class));

$app->get('/login', [UserController::class, 'login']);
$app->post('/login', [UserController::class, 'loginPost']);
$app->get('/logout', [UserController::class, 'logout']);
$app->get('/password', [UserController::class, 'password'])->add($auth);
$app->post('/password', [UserController::class, 'passwordPost'])->add($auth);

$app->get('/', [FolderController::class, 'view'])->add($auth);
$app->get('/folders[/{id}]', [FolderController::class, 'view'])->add($auth);
$app->post('/folders/create', [FolderController::class, 'createPost'])->add($auth);
$app->post('/folders/update', [FolderController::class, 'updatePost'])->add($auth);
$app->post('/folders/delete', [FolderController::class, 'deletePost'])->add($auth);

$app->get('/files[/{id}]', [FileController::class, 'view'])->add($auth);
$app->post('/files/create', [FileController::class, 'createPost'])->add($auth);
$app->post('/files/update', [FileController::class, 'updatePost'])->add($auth);
$app->post('/files/content', [FileController::class, 'updateContentPost'])->add($auth);
$app->post('/files/delete', [FileController::class, 'deletePost'])->add($auth);

$app->run();
