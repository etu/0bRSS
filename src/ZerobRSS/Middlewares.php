<?php
namespace ZerobRSS;

use \Auryn\Injector;
use \Slim\Slim;

class Middlewares
{
    /** @var Injector */
    private $injector;

    /** @var Slim */
    private $slim;

    public function __construct(Injector $injector, Slim $slim)
    {
        $this->injector = $injector;
        $this->slim = $slim;
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
    public function db()
    {
        $injector = $this->injector;

        return function () use ($injector) {
            $config = require(PROJECT_ROOT.'/config.php');
            $dbConfig = $config['environments']['database'];


            // Connect to database
            $dbalConfig = new \Doctrine\DBAL\Configuration();
            $dbalConn = \Doctrine\DBAL\DriverManager::getConnection([
                'url' => $dbConfig['adapter'].'://'.$dbConfig['user'].':'.$dbConfig['pass'].'@'.$dbConfig['host'].':'
                        .$dbConfig['port'].'/'.$dbConfig['name'].'?charset='.$dbConfig['charset']
            ], $dbalConfig);


            // Set default fetch-mode to fetch objects
            $dbalConn->setFetchMode(\PDO::FETCH_OBJ);


            // Share \Doctrine\DBAL\Connection
            $injector->share($dbalConn);
        };
    }



    /**
     * Middleware to init session and authenticate the user
     */
    public function auth($group)
    {
        $slim = $this->slim;

        return function () use ($group, $slim) {
            session_start();

            if (true === $group) {
                return true;
            }

            if (!in_array($group, $_SESSION['user']['groups'])) {
                $slim->log->info('User not member of group: '.$group);

                // Redirect to loginpage
                header('Location: '.$slim->request->getRootUri().'/login');
                #$slim->response->headers->set('Location', $slim->request->getRootUri().'/login');

                exit;
            }

            // Otherwise, everything is fine
            return true;
        };
    }
}
