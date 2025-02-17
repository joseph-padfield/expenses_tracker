<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Interfaces\UsersModelInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Validation\UserValidation;

class UserController extends Controller
{
    private UsersModelInterface $model;

    public function __construct(UsersModelInterface $model)
    {
        $this->model = $model;
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $error = UserValidation::validateRegister($data);

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$name || !$email || !$password)
        {
            return $this->respondWithJson($response, ['error' => 'All fields are required.']);
        }

        if ($this->model->userExists($email))
        {
            return $this->respondWithJson($response, ['error' => 'Email already exists.'], 409);
        }

        if ($this->model->createUser($name, $email, $password))
        {
            return $this->respondWithJson($response, ['success' => 'User registered successfully.'], 201);
        }

        return $this->respondWithJson($response, ['error' => 'User registration failed.'], 500);
    }
}