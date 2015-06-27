<?php

define('PROJECT_ROOT', realpath(__DIR__.'/..'));

require(PROJECT_ROOT.'/vendor/autoload.php');



error_reporting(E_ALL);
ini_set('display_errors', 1);



/**
 * Load Configruation
 */
$config = require(PROJECT_ROOT.'/config.php');



/**
 * Prepare dependency injector
 */
$injector = new \Auryn\Injector();



/**
 * Prepare Logger
 */
$logger = new \Flynsarmy\SlimMonolog\Log\MonologWriter([
    'handlers' => [
        new \Monolog\Handler\StreamHandler(PROJECT_ROOT.'/logs/'.date('Y-m-d').'.log')
    ]
]);



/**
 * Prepare Database Connection
 */
$dbConfig = $config['environments']['database'];

$dbalConfig = new \Doctrine\DBAL\Configuration();
$dbalConn = \Doctrine\DBAL\DriverManager::getConnection([
    'url' => $dbConfig['adapter'].'://'.$dbConfig['user'].':'.$dbConfig['pass'].'@'.$dbConfig['host'].':'
            .$dbConfig['port'].'/'.$dbConfig['name'].'?charset='.$dbConfig['charset']
], $dbalConfig);

$injector->share($dbalConn);                       // Share \Doctrine\DBAL\Connection
$injector->share($dbalConn->createQueryBuilder()); // Share \Doctrine\DBAL\Query\QueryBuilder



/**
 * Prepare Slim
 */
$slim = new \Slim\Slim([
    'log.writer' => $logger,
    'view' => new \Slim\Views\Twig(),
    'templates.path' => PROJECT_ROOT.'/src/views'
]);
$injector->share($slim);



/**
 * Prepare View
 */
$slim->view->parserOptions['debug'] = true;
#$slim->view->parserOptions['cache'] = $slim->config('templates.path').'/cache';
$slim->view->parserExtensions[] = new \Slim\Views\TwigExtension();



/**
 * Controller loading closure
 */
$controllerLoader = (function ($controller, $method) use ($injector) {
    return (function () use ($controller, $method, $injector) {
        $controller = $injector->make('ZerobRSS\Controllers\\'.$controller);

        return call_user_func_array([$controller, $method], func_get_args());
    });
});



/**
 * Prepare Routes
 */
$slim->get('/',                 $controllerLoader('Root', 'index'));
$slim->get('/assets/css/:file', $controllerLoader('Scss', 'get'));



/**
 * Run application
 */
$slim->run();
