<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\UsersModel;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;
use PDO;
use PDOStatement;

class UsersModelTest extends TestCase
{
    private $pdoMock;
    private $pdoFactoryMock;
    private $containerMock;
    private $usersModel;

    protected function setUp(): void
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->pdoMock = $this->createMock(PDO::class);

        $this->pdoFactoryMock = $this->createMock(PDOFactory::class);
        $this->pdoFactoryMock->method('__invoke')->willReturn($this->pdoMock);

        $this->usersModel = new UsersModel($this->pdoFactoryMock, $this->containerMock);
    }

    public function testUserExistsReturnsTrueWhenUserExists()
    {
        $email = "test@example.com";

        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetch')->willReturn(['id' => 1]);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $result = $this->usersModel->userExists($email);

        $this->assertTrue($result);
    }

    public function testUserExistsReturnsFalseWhenUserDoesNotExist()
    {
        $email = "nonexistent@example.com";

        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetch')->willReturn(false);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $result = $this->usersModel->userExists($email);

        $this->assertFalse($result);
    }

    public function testCreateUserReturnsTrueOnSuccess()
    {
        $statementMock = $this->createMock(PDOStatement::class);

        // Mock all expected method calls
        $statementMock->method('bindParam')->willReturn(true);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('rowCount')->willReturn(1); // ✅ Mock rowCount() returning > 0

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $result = $this->usersModel->createUser("John Doe", "john@example.com", "securepassword");

        $this->assertTrue($result);
    }

    public function testGetUserByEmailReturnsUserData()
    {
        $email = "test@example.com";
        $mockUser = ['id' => 1, 'name' => 'John Doe', 'email' => 'test@example.com', 'password' => 'hashedpassword'];

        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetch')->willReturn($mockUser);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $user = $this->usersModel->getUserByEmail($email);

        $this->assertIsArray($user);
        $this->assertEquals('John Doe', $user['name']);
        $this->assertEquals('test@example.com', $user['email']);
    }

    public function testGetUserByEmailReturnsNullWhenUserNotFound()
    {
        $email = "unknown@example.com";

        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('execute')->willReturn(true);
        $statementMock->method('fetch')->willReturn(false);

        $this->pdoMock->method('prepare')->willReturn($statementMock);

        $user = $this->usersModel->getUserByEmail($email);

        $this->assertNull($user);
    }
}