<?php

declare(strict_types=1);

namespace Yii\Middleware\Tests\Support;

use HttpSoft\Message\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response(302, ['X-Request-Handler' => 'true']);
        $response->getBody()->write('Request Handler Content');

        return $response;
    }
}
