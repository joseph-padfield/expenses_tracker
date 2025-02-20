<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\ExpensesModel;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;
use PDO;
use PDOStatement;

class ExpensesModelTest extends TestCase
{
    private $pdoMock;
    private $pdoFactoryMock;
    private $containerMock;
    private $expensesModel;

    protected function setUp(): void
    {
        // Mock ContainerInterface
        $this->containerMock = $this->createMock(ContainerInterface::class);

        // Mock PDO
        $this->pdoMock = $this->createMock(PDO::class);

        // Mock PDOFactory to return a mock PDO instance
        $this->pdoFactoryMock = $this->createMock(PDOFactory::class);
        $this->pdoFactoryMock->method('__invoke')->willReturn($this->pdoMock);

        // Inject mocks into ExpensesModel
        $this->expensesModel = new ExpensesModel($this->pdoFactoryMock, $this->containerMock);
    }

    public function testGetExpensesReturnsArray()
    {
        $userId = 1;
        $mockData = [
            ['id' => 1, 'amount' => 50.0, 'category_id' => 2, 'description' => 'Lunch'],
            ['id' => 2, 'amount' => 20.0, 'category_id' => 3, 'description' => 'Transport'],
        ];

        // Mock PDOStatement
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetchAll')->willReturn($mockData);

        // Ensure PDO prepare() returns our mocked statement
        $this->pdoMock->method('prepare')->willReturn($statementMock);

        // Call method
        $expenses = $this->expensesModel->getExpenses($userId);

        // Assertions
        $this->assertIsArray($expenses);
        $this->assertCount(2, $expenses);
        $this->assertEquals(50.0, $expenses[0]['amount']);
    }

    public function testCreateExpenseReturnsTrueOnSuccess()
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $result = $this->expensesModel->createExpense(1, 2, 30.5, "Coffee", "2024-02-15");

        $this->assertTrue($result);
    }

    public function testUpdateExpenseReturnsTrueOnSuccess()
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $result = $this->expensesModel->updateExpense(1, 1, 2, 40.0, "Dinner", "2024-02-16");

        $this->assertTrue($result);
    }

    public function testDeleteExpenseReturnsTrueOnSuccess()
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $result = $this->expensesModel->deleteExpense(1, 1);

        $this->assertTrue($result);
    }

    public function testGetExpenseByIdReturnsExpense()
    {
        $expenseId = 1;
        $userId = 1;
        $mockExpense = ['id' => 1, 'amount' => 50.0, 'category_id' => 2, 'description' => 'Lunch'];

        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetch')->willReturn($mockExpense);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $expense = $this->expensesModel->getExpenseById($expenseId, $userId);

        $this->assertIsArray($expense);
        $this->assertEquals(50.0, $expense['amount']);
    }
}