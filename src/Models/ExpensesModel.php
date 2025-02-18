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
        $sql = $this->db->prepare("
            SELECT expenses.*, categories.name AS category_name                                            
            FROM `expenses` 
            JOIN `categories` ON expenses.category_id = categories.id
            WHERE expenses.user_id = :user_id");
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExpenseById(int $expenseId, int $userId): ?array
    {
        $sql = $this->db->prepare("
            SELECT expenses.*, categories.name 
            FROM `expenses` 
            JOIN `categories` ON expenses.category_id = categories.id
            WHERE expenses.id = :id AND expenses.user_id = :user_id");
        $sql->bindParam(":id", $expenseId, PDO::PARAM_INT);
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->execute();
        $expense = $sql->fetch(PDO::FETCH_ASSOC);
        return $expense ?: null;
    }

    public function createExpense(int $userId, int $categoryId, float $amount, string $description, string $date): bool
    {
        $sql = $this->db->prepare('INSERT INTO expenses (user_id, category_id, amount, description, date) VALUES (:user_id, :category_id, :amount, :description, :date)');
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
        $sql->bindParam(":amount", $amount, PDO::PARAM_STR);
        $sql->bindParam(":description", $description, PDO::PARAM_STR);
        $sql->bindParam(":date", $date, PDO::PARAM_STR);
        return $sql->execute();
    }

    public function updateExpense(int $expenseId, int $userId, int $categoryId, float $amount, string $description, string $date): bool
    {
        $sql = $this->db->prepare('UPDATE expenses SET category = :category, amount = :amount, description = :description, date = :date WHERE id = :id AND user_id = :user_id');
        $sql->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
        $sql->bindParam(":amount", $amount, PDO::PARAM_STR);
        $sql->bindParam(":description", $description, PDO::PARAM_STR);
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

    public function getTotalExpenses(int $userId): float
    {
        $sql = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE user_id = :user_id");
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->execute();
        return (float) $sql->fetchColumn();
    }

    public function getTotalExpensesByCategory(int $userId): array
    {
        $sql = $this->db->prepare("
        SELECT categories.name AS category_name, COALESCE(SUM(amount), 0) AS total
        FROM expenses 
        JOIN categories ON expenses.category_id = categories.id
        WHERE expenses.user_id = :user_id
        GROUP BY category_name
        ORDER BY total DESC
        ");
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalExpensesByMonth(int $userId): array
    {

    }
}