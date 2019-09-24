<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;
use ZerobRSS\Config;
use ZerobRSS\Slim\ErrorHandler;
use ZerobRSS\Slim\ShutdownHandler;

require_once(__DIR__.'/../vendor/autoload.php');

error_reporting(E_ALL);
ini_set('display_errors', '1');


/**
 * Buffer output for error handling reasons
 */
ob_start();


/**
 * Instantiate PHP-DI ContainerBuilder
 */
$containerBuilder = new ContainerBuilder();


/**
 * Set up config
 */
(require(__DIR__.'/../src/bootstrap/config.php'))($containerBuilder);


/**
 * Set up dependencies
 */
(require(__DIR__.'/../src/bootstrap/dependencies.php'))($containerBuilder);


/**
 * Build PHP-DI Container instance
 */
$container = $containerBuilder->build();


/**
 * Instantiate the app
 */
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();


/**
 * Register routes
 */
(require(__DIR__.'/../src/bootstrap/routes.php'))($app);


/** @var bool $debug */
$debug = $container->get(Config::class)->debug;


/**
 * Create Request object from globals
 */
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();


/**
 * Create Error Handler
 */
$responseFactory = $app->getResponseFactory();
$errorHandler = new ErrorHandler($callableResolver, $responseFactory, $container->get(LoggerInterface::class));


/**
 * Create Shutdown Handler
 */
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $debug);
register_shutdown_function($shutdownHandler);


/**
 * Add Routing Middleware
 */
$app->addRoutingMiddleware();


/**
 * Add Error Middleware
 */
$errorMiddleware = $app->addErrorMiddleware($debug, true, true);
$errorMiddleware->setDefaultErrorHandler($errorHandler);


/**
 * Parse incomming JSON and make it available.
 */
$app->add(\ZerobRSS\Slim\JsonBodyParserMiddleware::class);


/**
 * Run App & Emit Response
 */
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
