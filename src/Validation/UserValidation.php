<?php

declare(strict_types=1);

namespace App\Validation;

use App\Abstracts\Validation;

class UserValidation extends Validation
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

        return null;
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

        return null;
    }

    public static function validateUser(int $user, array $data): ?string
    {
        if ($user !== $data['user_id'])
        {
            return 'Invalid user ID.';
        }
        return null;
    }
}