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
 * Prepare Slim
 */
$slim = new \Slim\Slim([
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
$slim->get('/assets/js/:file',                                   $mws->controllerLoader('Js',     'get'));
$slim->get('/login',                                 $mws->db(), $mws->controllerLoader('Login',  'get'));
$slim->post('/login',           $mws->auth(true),    $mws->db(), $mws->controllerLoader('Login',  'post'));
$slim->get('/logout',                                            $mws->controllerLoader('Logout', 'get'));
$slim->get('/read/(:id)',       $mws->auth('users'), $mws->db(), $mws->controllerLoader('Read',   'get'));

/** Route: /api/v1 */
$slim->group('/api/v1', function () use ($slim, $mws) {
    /** Route: /api/v1/feeds */
    $slim->group('/feeds', function () use ($slim, $mws) {
        $slim->get('/',         $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'get'));
        $slim->get('/:id',      $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'get'));
        $slim->post('/',        $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'post'));
        $slim->put('/:id',      $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'put'));
        $slim->delete('/:id',   $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Feeds', 'delete'));

        /** Route: /api/v1/feeds/:id/articles */
        $slim->group('/:id/articles', function () use ($slim, $mws) {
            $slim->get('/',     $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Articles', 'get'));
        });
    });

    /** Route: /api/v1/articles */
    $slim->group('/articles', function () use ($slim, $mws) {
        $slim->get('/:aid',     $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Articles', 'getArticle'));
        $slim->put('/:aid',     $mws->auth('users'), $mws->db(), $mws->controllerLoader('Api\Articles', 'put'));
    });
});



/**
 * Run application
 */
$slim->run();
