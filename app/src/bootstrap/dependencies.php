<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        Twig::class => function (ContainerInterface $c) {
            $twig = new Twig(
                __DIR__.'/../views/',
                [
                    'cache' => __DIR__.'/../../cache/',
                    'debug' => $c->get('settings')['displayErrorDetails'],
                ]
            );

            $twig->addExtension(new DebugExtension());
            $twig->addExtension(new TwigExtension());

            return $twig;
        },

        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];

            $logger = new Logger($loggerSettings['name']);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);
};
