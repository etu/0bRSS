<?php
declare(strict_types=1);

namespace ZerobRSS\Slim;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Slim\Interfaces\CallableResolverInterface;

class ErrorHandler extends SlimErrorHandler
{
    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;

        parent::__construct($callableResolver, $responseFactory);
    }

    protected function logError(string $error): void
    {
        $this->logger->info($error);
    }
}
