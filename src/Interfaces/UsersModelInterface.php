<?php

namespace App\Interfaces;

interface UsersModelInterface
{
    public function userExists(string $email): bool;
    public function createUser(string $name, string $email, string $password): bool;
    public function getUserByEmail(string $email): ?array;
}