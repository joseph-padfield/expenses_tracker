<?php

namespace App\Interfaces;

interface ExpensesModelInterface
{
    public function getExpenses(int $userId): array;
    public function getExpenseById(int $expenseId, int $userId): ?array;
    public function createExpense(int $userId, int $categoryId, float $amount, string $date): bool;
    public function updateExpense(int $expenseId, int $userId, int $categoryId, float $amount, string $date): bool;
    public function deleteExpense(int $expenseId, int $userId): bool;

}