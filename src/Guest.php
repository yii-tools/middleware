<?php

declare(strict_types=1);

namespace Yii\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;

/**
 * Middleware that checks if the user is a guest.
 */
final class Guest implements MiddlewareInterface
{
    public function __construct(
        private readonly bool $isGuest,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isGuest === false) {
            return $this->responseFactory->createResponse(Status::FOUND)->withHeader(Header::LOCATION, '/');
        }

        return $handler->handle($request);
    }
}
