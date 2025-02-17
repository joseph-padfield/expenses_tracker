<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;

class CategoriesModel
{
    private PDO $db;

    public function __construct(PDOFactory $db, ContainerInterface $container)
    {
        $this->db = $db($container);
    }

}