<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;

class Logout
{
    /** @var Slim */
    private $slim;

    public function __construct(Slim $slim)
    {
        $this->slim = $slim;
    }

    public function get()
    {
        $cookieName = session_name();
        $sessionParams = session_get_cookie_params();

        // Unset cookie in browser
        setcookie(
            $cookieName,
            false,
            1, // One second after unix-timestamp because 0 means until browser closes
            $sessionParams['path'],
            $sessionParams['domain'],
            $sessionParams['secure']
        );

        $this->slim->redirect($this->slim->request->getRootUri().'/login');
    }
}
