<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;
use App\Interfaces\ExpensesModelInterface;

class ExpensesModel implements ExpensesModelInterface
{
    private PDO $db;

    public function __construct(PDOFactory $db, ContainerInterface $container)
    {
        $this->db = $db($container);
    }

    public function getExpenses(int $userId): array
    {
        $sql = $this->db->prepare("SELECT * FROM `expenses` WHERE `user_id` = :user_id");
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExpenseById(int $expenseId, int $userId): ?array
    {
        $sql = $this->db->prepare("SELECT * FROM `expenses` WHERE `id` = :id AND `user_id` = :user_id");
        $sql->bindParam(":id", $expenseId, PDO::PARAM_INT);
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->execute();
        $expense = $sql->fetch(PDO::FETCH_ASSOC);
        return $expense ?: null;
    }

    public function createExpense(int $userId, int $categoryId, float $amount, string $date): bool
    {
        $sql = $this->db->prepare('INSERT INTO expenses (user_id, category_id, amount, date) VALUES (:user_id, :category_id, :amount, :date)');
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
        $sql->bindParam(":amount", $amount, PDO::PARAM_STR);
        $sql->bindParam(":date", $date, PDO::PARAM_STR);
        return $sql->execute();
    }

    public function updateExpense(int $expenseId, int $userId, int $categoryId, float $amount, string $date): bool
    {
        $sql = $this->db->prepare('UPDATE expenses SET category = :category, amount = :amount, date = :date WHERE id = :id AND user_id = :user_id');
        $sql->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
        $sql->bindParam(":amount", $amount, PDO::PARAM_STR);
        $sql->bindParam(":date", $date, PDO::PARAM_STR);
        $sql->bindParam(":id", $expenseId, PDO::PARAM_INT);
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        return $sql->execute();
    }

    public function deleteExpense(int $expenseId, int $userId): bool
    {
        $sql = $this->db->prepare('DELETE FROM expenses WHERE id = :id AND user_id = :user_id');
        $sql->bindParam(":id", $expenseId, PDO::PARAM_INT);
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        return $sql->execute();
    }
}