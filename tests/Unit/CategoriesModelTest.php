<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\CategoriesModel;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;
use PDO;
use PDOStatement;

class CategoriesModelTest extends TestCase
{
    private $pdoMock;
    private $pdoFactoryMock;
    private $containerMock;
    private $categoriesModel;

    protected function setUp(): void
    {
//        mock container interface
        $this->containerMock = $this->createMock(ContainerInterface::class);

//        mock pdo
        $this->pdoMock = $this->createMock(PDO::class);

//        mock pdoFactory to return a mock pdo instance
        $this->pdoFactoryMock = $this->createMock(PDOFactory::class);
        $this->pdoFactoryMock->method('__invoke')->willReturn($this->pdoMock);

//        inject mock into categories model
        $this->categoriesModel = new CategoriesModel($this->pdoFactoryMock, $this->containerMock);
    }

    public function testGetCategoriesReturnsArray()
    {
        $mockData = [
            ['id' => 1, 'name' => 'Housing'],
            ['id' => 1, 'name' => 'Utilities'],
            ['id' => 1, 'name' => 'Groceries']
        ];

//        mock pdo statement
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetchAll')->willReturn($mockData);

//        ensure pdo prepare returns mocked statement
        $this->pdoMock->method('prepare')->willReturn($statementMock);

//        call method
        $categories = $this->categoriesModel->getCategories();

//        assertions
        $this->assertIsArray($categories);
        $this->assertCount(count($mockData), $categories);
        $this->assertEquals('Housing', $categories['0']['name']);
    }
}