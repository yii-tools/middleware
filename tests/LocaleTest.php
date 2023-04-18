<?php

declare(strict_types=1);

namespace Yii\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Yii\Middleware\Tests\Support\TestTrait;

final class LocaleTest extends TestCase
{
    use TestTrait;

    public function testProcessSetsDefaultLanguageForInvalidLanguage(): void
    {
        $path = '/xx-XX/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessSetsDefaultLanguageForInvalidPath(): void
    {
        $path = '/invalidPath/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessRedirecWithPathRootLanguage(): void
    {
        $path = '/';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('en', $this->translator->getLocale());
        $this->assertSame(302, $response->getStatusCode());

        $path = '/en';

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('en', $this->translator->getLocale());
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testProcessReturnsResponseInterface(): void
    {
        $this->createContainer();

        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($this->request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('en', $this->translator->getLocale());
    }

    public function testProcessWithDefaultLanguage(): void
    {
        $path = '/en/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('en', $this->translator->getLocale());
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testProcessWithLanguageIsValid(): void
    {
        $path = '/de/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('de', $this->translator->getLocale());
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testProcessWithNoLocalesReturnsRequestHandlerResponse(): void
    {
        $this->createContainer();

        $locale = new Locale($this->translator, $this->urlGenerator, [], []);
        $response = $locale->process($this->request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testUrlGeneratorInterfaceSetDefaulArguments(): void
    {
        $path = '/ru/login';

        $this->createContainer();

        $request = $this->request->withUri($this->request->getUri()->withPath($path));
        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $response = $locale->process($request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('ru', $this->translator->getLocale());
        $this->assertSame('/login?_language=ru', $this->urlGenerator->generate('login'));
    }

    public function testWithDefaultLocale(): void
    {

        $this->createContainer();

        $locale = new Locale($this->translator, $this->urlGenerator, ['en', 'ru'], []);
        $locale = $locale->withDefaultLocale('ru');
        $response = $locale->process($this->request, $this->handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
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

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('ru', $this->translator->getLocale());
        $this->assertSame('/login?_lang=ru', $this->urlGenerator->generate('login'));
    }
}
