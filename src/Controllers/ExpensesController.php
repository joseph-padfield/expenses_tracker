<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Interfaces\ExpensesModelInterface;
use App\Validation\UserValidation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Validation\ExpensesValidation;

class ExpensesController extends Controller
{
    private ExpensesModelInterface $model;

    public function __construct(ExpensesModelInterface $model)
    {
        $this->model = $model;
    }

    public function getExpenses(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $expenses = $this->model->getExpenses($user->sub);

        return $this->respondWithJson($response, $expenses);
    }

    public function getExpense(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $expenseId = (int) $args['id'];

        $expense = $this->model->getExpenseById($expenseId, $user->sub);
        if (!$expense) {
            return $this->respondWithJson($response, ['error' => 'Expense not found'], 404);
        }

        return $this->respondWithJson($response, $expense);
    }

    public function createExpense(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $userId = (int) $user->sub;
        $data = $request->getParsedBody();

        $error = array_filter([
            ExpensesValidation::validateData($data),
            UserValidation::validateUser($userId, $data)
        ]);

        if (!empty($error))
        {
            return $this->respondWithJson($response, ['message' => array_values($error)]);
        }

        // Validate required fields
        $category = $data['category'];
        $amount = $data['amount'];
        $description = $data['description'];
        $date = $data['date'];

        if (!$category || !$amount || !$description || !$date)
        {
            return $this->respondWithJson($response, ['error' => 'All fields are required.']);
        }



        $created = $this->model->createExpense($user->sub, $data['category'], (float) $data['amount'], $data['description'], $data['date']);

        if (!$created) {
            return $this->respondWithJson($response, ['error' => 'Failed to create expense'], 500);
        }

        return $this->respondWithJson($response, ['message' => 'Expense added successfully'], 201);
    }

    public function updateExpense(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $expenseId = (int) $args['id'];
        $data = $request->getParsedBody();

        if (!isset($data['category'], $data['amount'], $data['date'])) {
            return $this->respondWithJson($response, ['error' => 'Missing required fields'], 400);
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            return $this->respondWithJson($response, ['error' => 'Amount must be a positive number'], 400);
        }

        $updated = $this->model->updateExpense($expenseId, $user->sub, $data['category'], (float) $data['amount'], $data['description'], $data['date']);

        if (!$updated) {
            return $this->respondWithJson($response, ['error' => 'Failed to update expense'], 500);
        }

        return $this->respondWithJson($response, ['message' => 'Expense updated successfully']);
    }

    public function deleteExpense(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $expenseId = (int) $args['id'];

        $deleted = $this->model->deleteExpense($expenseId, $user->sub);
        if (!$deleted) {
            return $this->respondWithJson($response, ['error' => 'Failed to delete expense'], 500);
        }

        return $this->respondWithJson($response, ['message' => 'Expense deleted successfully']);
    }
}