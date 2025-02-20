<?php

declare(strict_types=1);

namespace Tests\Integration;

use Slim\Factory\AppFactory;
use Tests\TestCase;
use App\Models\CategoriesModel;
use App\Interfaces\CategoriesModelInterface;
use Slim\App;

class CategoriesControllerTest extends TestCase
{
    private $app;
    private $categoriesModelMock;

    protected function setUp(): void
    {
//        boot up slim app
        $this->app = $this->getAppInstance();

//        mock categories model interface
        $this->categoriesModelMock = $this->createMock(CategoriesModelInterface::class);

//        replace real categories model with our mock in the container
        $this->app->getContainer()->set(CategoriesModelInterface::class, $this->categoriesModelMock);
    }
}