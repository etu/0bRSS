<?php
declare(strict_types=1);

namespace ZerobRSS\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use ZerobRSS\Config;

class Js
{
    /** @var Config */
    private $config;

    /** @var StreamFactory */
    private $streamFactory;

    public function __construct(Config $config, StreamFactory $streamFactory)
    {
        $this->config = $config;
        $this->streamFactory = $streamFactory;
    }

    public function __invoke(Request $request, Response $response, array $args = []) : Response
    {
        // Find file paths
        $jsPath = $this->config->projectRoot.'/public/assets/js';
        $jsFile = $jsPath.'/'.$args['file'];

        // Return 404 if file isn't found
        if (!file_exists($jsFile)) {
            return $response->withStatus(404);
        }

        // Set header
        $response = $response->withHeader('Content-Type', 'application/javascript');

        // Return data
        return $response->withBody($this->streamFactory->createStreamFromFile($jsFile));
    }
}
