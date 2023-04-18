<?php

declare(strict_types=1);

namespace Yii\Middleware\Tests\Locale;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Yii\Middleware\Locale;
use Yii\Middleware\Tests\Support\TestTrait;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class MiddlewareTest extends TestCase
{
    use TestTrait;

    public function testProcessSetsDefaultLanguageForInvalidLanguage(): void
    {
        $path = '/eng/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('en', $this->translator->getLocale());

        $path = '/eng-US/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('en', $this->translator->getLocale());

        $path = '/english/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('en', $this->translator->getLocale());

        $path = '/EN-US/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessSetsDefaultLanguageForInvalidPath(): void
    {
        $path = '/invalidPath/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessRedirectWithPathRootLanguage(): void
    {
        $path = '/';

        $this->createContainer();

        $request = new ServerRequest(method: 'GET', uri: $path);
        $request = $request->withUri($request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/en/', $response->getHeaderLine('Location'));
        $this->assertSame('en', $this->translator->getLocale());

        $path = '/en';

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/en/', $response->getHeaderLine('Location'));
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessRedirectWithPathRootLanguageWithMethodPost(): void
    {
        $path = '/';

        $this->createContainer();

        $request = new ServerRequest(method: 'POST', uri: $path);
        $request = $request->withUri($request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/en/', $response->getHeaderLine('Location'));
        $this->assertSame('en', $this->translator->getLocale());

        $path = '/en';

        $request = new ServerRequest(method: 'POST', uri: $path);
        $request = $request->withUri($request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/en/', $response->getHeaderLine('Location'));
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessReturnsResponseInterface(): void
    {
        $this->createContainer();

        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($this->request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/en/', $response->getHeaderLine('Location'));
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessWithDefaultLanguage(): void
    {
        $path = '/en/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/en/login', $response->getHeaderLine('Location'));
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessWithLanguageIsValid(): void
    {
        $path = '/de/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/de/login', $response->getHeaderLine('Location'));
        $this->assertSame('de', $this->translator->getLocale());

        $path = '/aa/login';

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/aa/login', $response->getHeaderLine('Location'));
        $this->assertSame('aa', $this->translator->getLocale());


        $path = '/DE/login';

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/de/login', $response->getHeaderLine('Location'));
        $this->assertSame('de', $this->translator->getLocale());
    }

    public function testProcessWithNotLocalesReturnsRequestHandlerResponse(): void
    {
        $this->createContainer();

        $locale = new Locale($this->translator, $this->urlGenerator, [], []);
        $response = $locale->process($this->request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('en-US', $this->translator->getLocale());
    }

    public function testProcessWithoutLanguage(): void
    {
        $path = '/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/en/login', $response->getHeaderLine('Location'));
        $this->assertSame('en', $this->translator->getLocale());
        $this->assertSame('/login?_language=', $this->urlGenerator->generate('login'));
    }

    public function testUrlGeneratorInterfaceSetDefaultArguments(): void
    {
        $path = '/ru/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/ru/login', $response->getHeaderLine('Location'));
        $this->assertSame('/login?_language=ru', $this->urlGenerator->generate('login'));
        $this->assertSame('ru', $this->translator->getLocale());
    }

    public function testWithDefaultLocale(): void
    {
        $this->createContainer();

        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $locale = $locale->withDefaultLanguage('ru');
        $response = $locale->process($this->request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/ru/', $response->getHeaderLine('Location'));
        $this->assertSame('ru', $this->translator->getLocale());
    }

    public function testWithLocaleArgument(): void
    {
        $path = '/ru/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $locale = $locale->withLocaleArgument('_lang');
        $response = $locale->process($request, $this->handler);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/ru/login', $response->getHeaderLine('Location'));
        $this->assertSame('ru', $this->translator->getLocale());
        $this->assertSame('/login?_lang=ru', $this->urlGenerator->generate('login'));
    }
}
