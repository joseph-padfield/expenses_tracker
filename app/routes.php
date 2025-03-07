<?php
declare(strict_types=1);

use App\Controllers\CategoriesController;
use App\Controllers\LoginController;
use Slim\App;
use Slim\Views\PhpRenderer;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Middleware\JWTMiddleware;
use App\Controllers\UserController;
use App\Controllers\ExpensesController;

return function (App $app) {

    // Handle OPTIONS requests globally
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5173') // Set allowed frontend origin
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
			->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withStatus(200);
    });

    // Global CORS Middleware
    $app->add(function ($request, $handler) {
        $response = $handler->handle($request);

        return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5173') // Allow only frontend
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
			->withHeader('Access-Control-Allow-Credentials', 'true');
    });

    $container = $app->getContainer();

    $app->get('/', function ($request, $response, $args) use ($container) {
        $renderer = $container->get(PhpRenderer::class);
        return $renderer->render($response, "index.php", $args);
    });

    // Users routes
    $app->group('/users', function (Group $group) use ($container) {
        $group->post('/register', [$container->get(UserController::class), 'register']);
        $group->post('/login', [$container->get(LoginController::class), 'login']);
    });

    // Expenses routes
    $app->group('/expenses', function (Group $group) use ($container) {
        $group->get('', [$container->get(ExpensesController::class), 'getExpenses']);
        $group->get('/total', [$container->get(ExpensesController::class), 'getTotalExpenses']);
        $group->get('/total/category', [$container->get(ExpensesController::class), 'getTotalExpensesByCategory']);
        $group->get('/total/month', [$container->get(ExpensesController::class), 'getTotalExpensesByMonth']);
        $group->get('/{id}', [$container->get(ExpensesController::class), 'getExpense']);
        $group->post('', [$container->get(ExpensesController::class), 'createExpense']);
        $group->put('/{id}', [$container->get(ExpensesController::class), 'updateExpense']);
        $group->delete('/{id}', [$container->get(ExpensesController::class), 'deleteExpense']);
    })->add($container->get(JWTMiddleware::class));

    // Categories routes
    $app->group('/categories', function (Group $group) use ($container) {
        $group->get('', [$container->get(CategoriesController::class), 'getCategories']);
    });
};