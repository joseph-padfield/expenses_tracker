<?php

declare(strict_types=1);

namespace App\Validation;

use App\Abstracts\Validation;

class ExpensesValidation extends Validation
{
    public static function validateData(array $data): ?string
    {
        $requiredFields = ['category', 'amount', 'description', 'date'];
        $error = self::validateRequiredFields($data, $requiredFields);
        if ($error) return $error;

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            return 'Amount must be a positive number.';
        }

        // Validate date using a separate function
        return self::validateDate($data['date']);
    }

    public static function validateDate(string $date): ?string
    {
        $date = trim($date); // Remove spaces

        // Ensure format is YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return 'Invalid date format. Use YYYY-MM-DD.';
        }

        // Check if the date is valid
        $parts = explode("-", $date);
        if (count($parts) !== 3) {
            return "Invalid date format.";
        }

        list($year, $month, $day) = $parts;
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            return "Invalid date.";
        }

        // Ensure date is not in the future
        if (strtotime($date) > time()) {
            return "Date cannot be in the future.";
        }

        return null; // No errors
    }
}