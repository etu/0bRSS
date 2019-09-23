<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use ZerobRSS\Config;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        Config::class => function (ContainerInterface $c) : Config {
            $configArray = [
                'projectRoot' => realpath(__DIR__.'/../../'),
                'logger' => [
                    'name' => '0bRSS',
                    'path' => realpath(__DIR__.'/../../logs').'/app.log',
                    'level' => Logger::DEBUG,
                ]
            ];

            $configFile = $configArray['projectRoot'].'/config.php';

            if (!file_exists($configFile)) {
                throw new Exception('Config file: '.$configFile.' not found.');
            }

            return new Config(array_replace_recursive(
                $configArray,
                require($configFile)
            ));
        },
    ]);
};
