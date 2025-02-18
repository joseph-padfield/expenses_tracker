<?php

namespace App\Abstracts;

class Validation
{
    public static function validateRequiredFields(array $data, array $requiredFields): ?string
    {
        $errors = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "The field '$field' is required.";
            }
        }

        return !empty($errors) ? implode(" ", $errors) : null;
    }

    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isValidPassword(string $password): bool
    {
        return strlen($password) >= 8;
    }
}