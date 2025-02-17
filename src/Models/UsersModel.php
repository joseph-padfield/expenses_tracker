<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\UsersModelInterface;
use PDO;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;

class UsersModel implements UsersModelInterface
{
    private PDO $db;

    public function __construct(PDOFactory $db, ContainerInterface $container)
    {
        $this->db = $db($container);
    }

//check if user exists
    public function userExists(string $email): bool
    {
        $sql = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $sql->bindParam(':email', $email);
        $sql->execute();
        return (bool)$sql->fetch();
    }

//create new user

    public function createUser(string $name, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $sql->bindParam(':name', $name);
        $sql->bindParam(':email', $email);
        $sql->bindParam(':password', $hashedPassword);
        if ($sql->execute())
        {
            return $sql->rowCount() > 0;
        }
        return false;
    }

//get user by email
    public function getUserByEmail(string $email): ?array
    {
        $sql = $this->db->prepare("SELECT id, name, email, password FROM users WHERE email = :email");
        $sql->bindParam(':email', $email);
        $sql->execute();

        $user = $sql->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}