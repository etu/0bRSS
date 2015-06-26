<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;

class Root
{
    /** @var Slim */
    private $slim;

    public function __construct(Slim $slim)
    {
        $this->slim = $slim;
    }

    public function index()
    {
        $this->slim->log->info('Slim "/" route');

        $this->slim->render(
            'index.twig',
            ['name' => '0bRSS']
        );
    }
}
