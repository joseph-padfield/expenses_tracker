<?php
declare(strict_types=1);

use App\Controllers\LoginController;
use Slim\App;
use Slim\Views\PhpRenderer;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Middleware\JWTMiddleware;

use App\Controllers\CreateUserController;


return function (App $app) {

    $app->add(function($request, $handler)
    {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });

    $container = $app->getContainer();

    $app->get('/', function ($request, $response, $args) use ($container)
    {
        $renderer = $container->get(PhpRenderer::class);
        return $renderer->render($response, "index.php", $args);
    });

//    users routes
    $app->group('/users', function (Group $group) use ($container)
    {
       $group->post('/register', [$container->get(CreateUserController::class), 'register']);
       $group->post('/login', [$container->get(LoginController::class), 'login']);
    });

//    expenses routes
    $app->group('/expenses', function (Group $group) use ($container)
    {
//EXPENSES CONTROLLERS GO HERE
    })->add($container->get(JWTMiddleware::class));

//    categories routes

};
