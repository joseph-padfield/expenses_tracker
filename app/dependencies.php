<?php
declare(strict_types=1);

use App\Factories\LoggerFactory;
use App\Factories\PDOFactory;
use App\Factories\RendererFactory;
use App\Models\ExpensesModel;
use App\Models\UsersModel;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use App\Interfaces\UsersModelInterface;
use App\Controllers\UserController;
use App\Controllers\LoginController;
use App\Middleware\JWTMiddleware;
use App\Interfaces\ExpensesModelInterface;
use App\Controllers\ExpensesController;

return function (ContainerBuilder $containerBuilder) {
    $container = [];

    $container[LoggerInterface::class] = DI\factory(LoggerFactory::class);
    $container[PhpRenderer::class] = DI\factory(RendererFactory::class);
    $container[PDO::class] = DI\factory(PDOFactory::class);
    $container[UsersModelInterface::class] = DI\autowire(UsersModel::class);
    $container[ExpensesModelInterface::class] = DI\autowire(ExpensesModel::class);
    $container[UserController::class] = DI\autowire(UserController::class);
    $container[LoginController::class] = DI\autowire(LoginController::class);
    $container[ExpensesController::class] = DI\autowire(ExpensesController::class);
    $containerBuilder->addDefinitions($container);
    $containerBuilder->addDefinitions([
        JWTMiddleware::class => function ($container)
        {
            $settings = $container->get('settings');
            return new JWTMiddleware($settings);
        }
    ]);
};
