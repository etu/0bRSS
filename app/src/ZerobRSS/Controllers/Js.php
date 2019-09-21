<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;

class Js
{
    /** @var Slim */
    private $silm;

    public function __construct(Slim $slim)
    {
        $this->slim = $slim;
    }

    public function get($jsFile)
    {
        $jsPath = PROJECT_ROOT.'/http/assets/js';
        $jsFile = $jsPath.'/'.$jsFile;

        $this->slim->response->headers->set('Content-Type', 'application/javascript');
        echo file_get_contents($jsFile);
    }
}
