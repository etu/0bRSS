<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;
use \Leafo\ScssPhp\Compiler;

class Scss
{
    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * @var Slim
     */
    private $slim;

    public function __construct(Compiler $compiler, Slim $slim)
    {
        $this->compiler = $compiler;
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

        $this->compiler->addImportPath($scssPath);
        echo $this->compiler->compile(file_get_contents($scssFile));
    }
}
