<?php
declare(strict_types=1);

use App\Factories\LoggerFactory;
use App\Factories\PDOFactory;
use App\Factories\RendererFactory;
use App\Models\UsersModel;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use App\Interfaces\UsersModelInterface;
use App\Controllers\CreateUserController;
use App\Controllers\LoginController;
use App\Middleware\JWTMiddleware;

return function (ContainerBuilder $containerBuilder) {
    $container = [];

    $container[LoggerInterface::class] = DI\factory(LoggerFactory::class);
    $container[PhpRenderer::class] = DI\factory(RendererFactory::class);
    $container[PDO::class] = DI\factory(PDOFactory::class);
    $container[UsersModelInterface::class] = DI\autowire(UsersModel::class);
    $container[CreateUserController::class] = DI\autowire(CreateUserController::class);
    $container[LoginController::class] = DI\autowire(LoginController::class);
    $container[JWTMiddleware::class] = DI\autowire(JWTMiddleware::class);
    $containerBuilder->addDefinitions($container);
};
