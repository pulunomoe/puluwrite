<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Views\Twig;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Westsworld\TimeAgo;

return [
    PDO::class => function (): PDO {
        $dsn = $_ENV['DB_DRIVER'];
        $dsn .= ':host=' . $_ENV['DB_HOST'];
        $dsn .= ';port=' . $_ENV['DB_PORT'];
        $dsn .= ';dbname=' . $_ENV['DB_NAME'];
        $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    },

    ResponseFactoryInterface::class => function (
        ContainerInterface $container,
    ): DecoratedResponseFactory {
        $responseFactory = $container->get(ResponseFactory::class);
        $streamFactory = $container->get(StreamFactory::class);
        return new DecoratedResponseFactory($responseFactory, $streamFactory);
    },

    TimeAgo::class => function (): TimeAgo {
        return new TimeAgo();
    },

    Twig::class => function (
        ContainerInterface $container,
    ): Twig {
        $twig = Twig::create(
            __DIR__ . '/../templates',
            [
                'cache' => $_ENV['DEBUG'] ? false : __DIR__ . '/../var/cache',
            ],
        );
        $twig->getEnvironment()->addGlobal('session', $_SESSION);
        $twig->getEnvironment()->addFunction(
            new TwigFunction('hasFlash', function (string $key): bool {
                $flash = $_SESSION['flash'] ?? [];
                return isset($flash[$key]);
            }),
        );
        $twig->getEnvironment()->addFunction(
            new TwigFunction('getFlash', function (string $key): string {
                $flash = $_SESSION['flash'] ?? [];
                unset($_SESSION['flash']);
                return $flash[$key] ?? '';
            }),
        );
        $twig->getEnvironment()->addFilter(
            new TwigFilter('timeago', function (string $date) use ($container): string {
                $timeAgo = $container->get(TimeAgo::class);
                return $timeAgo->inWordsFromStrings($date);
            }),
        );
        return $twig;
    },
];
