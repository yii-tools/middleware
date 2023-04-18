<?php

declare(strict_types=1);

namespace Yii\Middleware;

use PHPUnit\Framework\TestCase;
use Yii\Middleware\Guest;
use Yii\Middleware\Tests\Support\TestTrait;

final class GuestTest extends TestCase
{
    use TestTrait;

    public function testProcessReturnsRedirectResponseIfNotGuest()
    {
        $this->createContainer();

        $guest = new Guest(false, $this->responseFactory);
        $response = $guest->process($this->request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/', $response->getHeaderLine('Location'));
    }

    public function testProcessReturnsRedirectResponseIfGuest()
    {
        $this->createContainer();

        $guest = new Guest(true, $this->responseFactory);
        $response = $guest->process($this->request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('', $response->getHeaderLine('Location'));
    }
}
