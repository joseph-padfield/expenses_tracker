<?php

declare(strict_types=1);

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class JWTMiddleware
{
    private $secret;

    public function __construct($settings)
    {
        $this->secret = $settings['jwt']['secret'];
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches))
        {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(["error" => "Token not provided"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        try {
            $decoded = JWT::decode($matches[1], new Key($this->secret, 'HS256'));
            $request = $request->withAttribute("user", $decoded);
        }
        catch (\Exception $exception)
        {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(["error" => "Invalid token"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }
}