<?php

declare(strict_types=1);

namespace App\Validation;

class UserValidation
{
    public static function validateRegister(array $data): ?string
    {
        $requiredFields = ['name', 'email', 'password'];
        $error = self::validateRequiredFields($data, $requiredFields);
        if ($error) return $error;

        if (!self::isValidEmail($data['email'])) {
            return "Invalid email.";
        }

        if (!self::isValidPassword($data['password'])) {
            return "Password must be at least 8 characters.";
        }

        return null; // No validation errors
    }

    public static function validateLogin(array $data): ?string
    {
        $requiredFields = ['email', 'password'];
        $error = self::validateRequiredFields($data, $requiredFields);
        if ($error) return $error;

        if (!self::isValidEmail($data['email'])) {
            return "Invalid email.";
        }

        if (!self::isValidPassword($data['password'])) {
            return "Password must be at least 8 characters.";
        }

        return null; // No validation errors
    }

    private static function validateRequiredFields(array $data, array $requiredFields): ?string
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return "The field '$field' is required.";
            }
        }
        return null;
    }

    private static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private static function isValidPassword(string $password): bool
    {
        return strlen($password) >= 8;
    }
}