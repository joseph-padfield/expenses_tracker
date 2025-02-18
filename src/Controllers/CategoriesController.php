<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Interfaces\CategoriesModelInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoriesController extends Controller
{
    private CategoriesModelInterface $model;

    public function __construct(CategoriesModelInterface $model)
    {
        $this->model = $model;
    }

    public function getCategories(Request $request, Response $response): Response
    {
        $categories = $this->model->getCategories();
        return $this->respondWithJson($response, $categories);
    }
}