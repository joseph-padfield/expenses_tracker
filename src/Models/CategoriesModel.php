<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\CategoriesModelInterface;
use PDO;
use App\Factories\PDOFactory;
use Psr\Container\ContainerInterface;

class CategoriesModel implements CategoriesModelInterface
{
    private PDO $db;

    public function __construct(PDOFactory $db, ContainerInterface $container)
    {
        $this->db = $db($container);
    }

    public function getCategories(): array
    {
        $sql = $this->db->prepare("SELECT * FROM categories");
        $sql->execute();
        return $sql->fetchAll();
    }

}