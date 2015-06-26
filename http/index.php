<?php

define('PROJECT_ROOT', realpath(__DIR__.'/..'));;

require(PROJECT_ROOT.'/vendor/autoload.php');



error_reporting(E_ALL);
ini_set('display_errors', 1);



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
 * Prepare Slim
 */
$slim = new \Slim\Slim([
    'log.writer' => $logger,
    'view' => new \Slim\Views\Twig(),
    'templates.path' => realpath(PROJECT_ROOT.'/src/views')
]);
$injector->share($slim);



/**
 * Prepare View
 */
$slim->view->parserOptions['debug'] = true;
#$slim->view->parserOptions['cache'] = $slim->config('templates.path').'/cache';
$slim->view->parserExtensions[] = new \Slim\Views\TwigExtension();



/**
 * Prepare Routes
 */
$slim->get('/', function () use ($injector) {
    $controller = $injector->make('ZerobRSS\Controllers\Root');

    return call_user_func_array([$controller, 'index'], func_get_args());
});



/**
 * Run application
 */
$slim->run();
