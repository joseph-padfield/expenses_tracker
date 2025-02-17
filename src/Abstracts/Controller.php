<?php

declare(strict_types=1);

namespace App\Abstracts;

use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{
    protected function respondWithJson(Response $response, $data, int $statusCode = 200): Response
    {
        $json = json_encode($data);
        if ($json === false) {
            $json = json_encode(['error' => 'Cannot encode JSON.']);
            $statusCode = 500;
        }

        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    protected function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function isValidPassword(string $password): bool
    {
        return strlen($password) <= 8;
    }

    protected function validateRequiredFields(array $data, array $requiredFields): ?string
    {
        foreach ($requiredFields as $field)
        {
            if (empty($data[$field])) return "The field '$field' is required.";
        }
        return null;
    }
}