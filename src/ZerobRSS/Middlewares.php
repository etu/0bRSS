<?php
namespace ZerobRSS;

use \Auryn\Injector;

class Middlewares
{
    /** @var Injector */
    private $injector;

    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }



    /**
     * Closure to load controllers
     */
    public function controllerLoader($controller, $method)
    {
        $injector = $this->injector;

        return function () use ($controller, $method, $injector) {
            $controller = $injector->make('ZerobRSS\Controllers\\'.$controller);

            return call_user_func_array([$controller, $method], func_get_args());
        };
    }



    /**
     * Middleware to initiate the database connection
     */
    public function initDb()
    {
        $injector = $this->injector;

        return function () use ($injector) {
            $config = require(PROJECT_ROOT.'/config.php');
            $dbConfig = $config['environments']['database'];


            $dbalConfig = new \Doctrine\DBAL\Configuration();

            $dbalConn = \Doctrine\DBAL\DriverManager::getConnection([
                'url' => $dbConfig['adapter'].'://'.$dbConfig['user'].':'.$dbConfig['pass'].'@'.$dbConfig['host'].':'
                        .$dbConfig['port'].'/'.$dbConfig['name'].'?charset='.$dbConfig['charset']
            ], $dbalConfig);


            $injector->share($dbalConn);                       // Share \Doctrine\DBAL\Connection
            $injector->share($dbalConn->createQueryBuilder()); // Share \Doctrine\DBAL\Query\QueryBuilder
        };
    }
}
