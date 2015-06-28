<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;

class Login
{
    /** @var Slim */
    private $slim;

    public function __construct(Slim $slim)
    {
        $this->slim = $slim;
    }

    public function get()
    {
        $this->slim->render(
            'login.twig'
        );
    }
}
