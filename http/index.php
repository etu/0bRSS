<?php
define('PROJECT_ROOT', realpath(__DIR__.'/..'));
require(PROJECT_ROOT.'/vendor/autoload.php');



error_reporting(E_ALL);
ini_set('display_errors', 1);



/**
 * Prepare dependency injector
 */
$injector = new \Auryn\Injector();
$injector->share($injector);



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
    'templates.path' => PROJECT_ROOT.'/src/views'
]);
$injector->share($slim);



/**
 * Prepare View
 */
$slim->view->parserOptions['debug'] = true;
#$slim->view->parserOptions['cache'] = $slim->config('templates.path').'/cache';
$slim->view->parserExtensions[] = new \Slim\Views\TwigExtension();
$slim->view->parserExtensions[] = new \Twig_Extension_Debug();



/**
 * Load the Middleware class with clasures for loading controllers and middlewares
 */
$mws = $injector->make('\ZerobRSS\Middlewares');



/**
 * Prepare Routes
 */
$slim->get('/',                 $mws->auth('users'), $mws->db(), $mws->controllerLoader('Index',  'get'));
$slim->get('/assets/css/:file',                                  $mws->controllerLoader('Scss',   'get'));
$slim->get('/login',                                 $mws->db(), $mws->controllerLoader('Login',  'get'));
$slim->post('/login',           $mws->auth(true),    $mws->db(), $mws->controllerLoader('Login',  'post'));
$slim->get('/logout',                                            $mws->controllerLoader('Logout', 'get'));
$slim->get('/read/(:id)',       $mws->auth('users'), $mws->db(), $mws->controllerLoader('Read',   'get'));

/** Route: /api */
$slim->group('/api', function () use ($slim, $mws) {
    /** Route: /api/feeds */
    $slim->group('/feeds', function () use ($slim, $mws) {
        $slim->get('/',         $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'get'));
        $slim->get('/:id',      $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'get'));
        $slim->post('/',        $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'post'));
        $slim->put('/:id',      $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'put'));
        $slim->delete('/:id',   $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'delete'));

        /** Route: /api/feeds/:id/articles */
        $slim->group('/:id/articles', function () use ($slim, $mws) {
            $slim->get('/',     $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Articles', 'get'));
        });
    });
});



/**
 * Run application
 */
$slim->run();
