<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\Interfaces\UsersModelInterface;
use App\Models\UsersModel;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UsersModelInterface::class => \DI\autowire(UsersModel::class),
    ]);
};