<?php

declare(strict_types=1);

namespace App\Validation;

use App\Abstracts\Validation;

class ExpensesValidation extends Validation
{
    public static function validateData (array $data): ?string
    {
        $requiredFields = ['category', 'amount', 'description', 'date'];
        $error = self::validateRequiredFields($data, $requiredFields);
        if ($error) return $error;

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            return 'Amount must be a positive number.';
        }
        return null;
    }
}