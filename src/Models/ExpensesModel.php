<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;

class ExpensesModel
{
    private PDOFactory $db;

    public function __construct(PDOFactory $db, ContainerInterface $container)
    {
        $this->db = $db;
    }
}