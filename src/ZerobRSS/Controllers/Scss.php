<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;
use \scssc;

class Scss
{
    /**
     * @var scssc
     */
    private $scssc;

    /**
     * @var Slim
     */
    private $slim;

    public function __construct(scssc $scssc, Slim $slim)
    {
        $this->scssc = $scssc;
        $this->slim  = $slim;
    }

    public function get($cssFile)
    {
        $scssPath = PROJECT_ROOT.'/src/scss';
        $scssFile = $scssPath.'/'.str_replace('css', 'scss', $cssFile);


        /** Pass the route if file don't exists, will result in 404 */
        if (!file_exists($scssFile)) {
            return $this->slim->pass();
        }


        $this->slim->response->headers->set('Content-Type', 'text/css');

        $this->scssc->addImportPath($scssPath);
        echo $this->scssc->compile(file_get_contents($scssFile));
    }
}
