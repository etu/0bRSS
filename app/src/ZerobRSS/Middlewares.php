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

    /** @var DbalConfig */
    private $dbalConfig;

    public function __construct(Injector $injector, Slim $slim)
    {
        $this->injector = $injector;
        $this->slim = $slim;
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
