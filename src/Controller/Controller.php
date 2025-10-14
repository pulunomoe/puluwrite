<?php

namespace App\Controller;

use App\Exception\TwigException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract readonly class Controller
{
    public function __construct(
        private Twig $twig,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @throws TwigException
     */
    protected function render(
        Response $response,
        string $template,
        array $data = [],
    ): ResponseInterface {
        try {
            return $this->twig->render($response, $template, $data);
        } catch (LoaderError|RuntimeError|SyntaxError $ex) {
            throw new TwigException($ex);
        }
    }

    protected function redirectWithSuccess(
        Response $response,
        string $path,
        string $message,
    ): ResponseInterface {
        $_SESSION['flash']['success'] = $message;
        return $response->withRedirect($path);
    }

    protected function redirectWithError(
        Response $response,
        string $path,
        string $message,
    ): ResponseInterface {
        $_SESSION['flash']['error'] = $message;
        return $response->withRedirect($path);
    }
}
