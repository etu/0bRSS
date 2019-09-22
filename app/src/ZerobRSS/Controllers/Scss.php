<?php
declare(strict_types=1);

namespace ZerobRSS\Controllers;

use Leafo\ScssPhp\Compiler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use ZerobRSS\Config;

class Scss
{
    /** @var Config */
    private $config;

    /** @var Compiler */
    private $compiler;

    /** @var StreamFactory */
    private $streamFactory;

    public function __construct(Config $config, Compiler $compiler, StreamFactory $streamFactory)
    {
        $this->config = $config;
        $this->compiler = $compiler;
        $this->streamFactory = $streamFactory;
    }

    public function __invoke(Request $request, Response $response, array $args = []) : Response
    {
        $scssPath = $this->config->projectRoot.'/src/scss';
        $scssFile = $scssPath.'/'.str_replace('css', 'scss', $args['file']);

        // Return 404 if file isn't found
        if (!file_exists($scssFile)) {
            return $response->withStatus(404);
        }

        $response = $response->withHeader('Content-Type', 'text/css');

        // Set up compiler
        $this->compiler->addImportPath($scssPath);
        $css = $this->compiler->compile(file_get_contents($scssFile));

        return $response->withBody($this->streamFactory->createStream($css));
    }
}
