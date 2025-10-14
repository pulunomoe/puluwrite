<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class AuthMiddleware
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        if (empty($_SESSION['user'])) {
            return $this->responseFactory
                ->createResponse(302)
                ->withHeader('Location', '/login');
        }

        return $handler->handle($request);
    }
}
