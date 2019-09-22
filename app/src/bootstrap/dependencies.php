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
use ZerobRSS\Config;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        Twig::class => function (ContainerInterface $c) {
            $twig = new Twig(
                $c->get(Config::class)->projectRoot.'/src/views',
                [
                    'cache' => $c->get(Config::class)->projectRoot.'/cache',
                    'debug' => $c->get(Config::class)->debug,
                ]
            );

            $twig->addExtension(new DebugExtension());
            $twig->addExtension(new TwigExtension());

            return $twig;
        },

        LoggerInterface::class => function (ContainerInterface $c) {
            $config = $c->get(Config::class);

            $logger = new Logger($config->logger->name);

            $handler = new StreamHandler($config->logger->path, $config->logger->level);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);
};
