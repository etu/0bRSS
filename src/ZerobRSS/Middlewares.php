<?php
namespace ZerobRSS;

use \Auryn\Injector;
use \Slim\Slim;
use \Doctrine\DBAL\Configuration as DbalConfig;

class Middlewares
{
    /** @var Injector */
    private $injector;

    /** @var Slim */
    private $slim;

    /** @var DbalConfig */
    private $dbalConfig;

    public function __construct(Injector $injector, Slim $slim, DbalConfig $dbalConfig)
    {
        $this->injector = $injector;
        $this->slim = $slim;
        $this->dbalConfig = $dbalConfig;
    }



    /**
     * Closure to load controllers
     */
    public function controllerLoader(string $controller, string $method)
    {
        $injector = $this->injector;

        return function () use ($controller, $method, $injector) {
            $controller = $injector->make('\ZerobRSS\Controllers\\'.$controller);

            return call_user_func_array([$controller, $method], func_get_args());
        };
    }



    /**
     * Middleware to initiate the database connection
     */
    public function db()
    {
        $injector = $this->injector;

        return function () use ($injector) {
            $config = require(PROJECT_ROOT.'/config.php');
            $dbConfig = $config['environments']['database'];


            // Connect to database
            $dbalConn = \Doctrine\DBAL\DriverManager::getConnection([
                'url' => $dbConfig['adapter'].'://'.$dbConfig['user'].':'.$dbConfig['pass'].'@'.$dbConfig['host'].':'
                        .$dbConfig['port'].'/'.$dbConfig['name'].'?charset='.$dbConfig['charset']
            ], $this->dbalConfig);


            // Set default fetch-mode to fetch objects
            $dbalConn->setFetchMode(\PDO::FETCH_OBJ);


            // Share \Doctrine\DBAL\Connection
            $injector->share($dbalConn);
        };
    }



    /**
     * Middleware to init session and authenticate the user
     */
    public function auth(string $group) : bool
    {
        $slim = $this->slim;

        return function () use ($group, $slim) {
            session_start();

            if (true === $group) {
                return true;
            }

            if (!isset($_SESSION['user']) || !in_array($group, $_SESSION['user']['groups'])) {
                $slim->log->info('User not member of group: '.$group);

                // Redirect to loginpage
                $slim->redirect($slim->request->getRootUri().'/login');

                exit;
            }

            // Otherwise, everything is fine
            return true;
        };
    }
}
