<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Interfaces\UsersModelInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use App\Validation\UserValidation;

class LoginController extends Controller
{
    private UsersModelInterface $model;

    public function __construct(UsersModelInterface $model)
    {
        $this->model = $model;
    }

    public function login(Request $request, Response $response): Response
    {
        $requiredFields = ['email', 'password'];

        $data = $request->getParsedBody();

        $error = UserValidation::validateLogin($data);

        if ($error)
        {
            return $this->respondWithJson($response, ['error' => $error], 400);
        }

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

//get user by email
        $user = $this->model->getUserByEmail($email);
        if (!$user || !password_verify($password, $user['password']))
        {
            return $this->respondWithJson($response, ['error' => 'Invalid credentials.'], 401);
        }

//generate JWT token
        $token = $this->generateJwtToken($user['id'], $user['email']);

        return $this->respondWithJson($response, [
            'message' => 'Successfully logged in',
			'username' => $user['name'],
            'token' => $token
        ], 200);

    }

    private function generateJwtToken(int $userId, string $email): string
    {
        $key = $_ENV['JWT_SECRET'];

        $payload = [
            "sub" => $userId,
            "email" => $email,
            "iat" => time(),
            "exp" => time() + 3600
        ];

		    $token = JWT::encode($payload, $key, 'HS256');

    return $token;
    }
}