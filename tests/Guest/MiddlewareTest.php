<?php

declare(strict_types=1);

namespace Yii\Middleware\Tests\Guest;

use PHPUnit\Framework\TestCase;
use Yii\Middleware\Guest;
use Yii\Middleware\Tests\Support\TestTrait;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class MiddlewareTest extends TestCase
{
    use TestTrait;

    public function testProcessReturnsRedirectResponseIfNotGuest(): void
    {
        $this->createContainer();

        $guest = new Guest(false, $this->responseFactory);
        $response = $guest->process($this->request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/', $response->getHeaderLine('Location'));
    }

    public function testProcessReturnsRedirectResponseIfGuest(): void
    {
        $this->createContainer();

        $guest = new Guest(true, $this->responseFactory);
        $response = $guest->process($this->request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('', $response->getHeaderLine('Location'));
    }
}
