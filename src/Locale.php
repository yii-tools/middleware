<?php

declare(strict_types=1);

namespace Yii\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use function str_contains;

/**
 * Middleware that sets the application language based on the client's preferred language.
 */
final class Locale implements MiddlewareInterface
{
    private string $defaultLanguage = 'en';
    private string $localeArgument = '_language';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly array $languages = [],
        private readonly array $ignoredUrls = []
    ) {
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->languages === []) {
            return $handler->handle($request);
        }

        $uri = $request->getUri();
        $path = $uri->getPath();
        $language = $this->getLanguage($path);

        if ($this->shouldIgnoreUrl($request) === false) {
            $this->setLocale($language);
        }

        if ($path === '/') {
            $request = $request->withUri($uri->withPath("/"));
        }

        if (
            ($path === "/$this->defaultLanguage" || $path === "/$this->defaultLanguage/") &&
            $request->getMethod() === Method::GET
        ) {
            return $handler->handle($request->withUri($uri->withPath('/')));
        }

        if ($language === $this->defaultLanguage) {
            $this->urlGenerator->setDefaultArgument($this->localeArgument, '');
            $request = $request->withUri($uri->withPath($this->getUrlPathWithLanguage($path, $language)));
        }

        $request = $request->withAttribute($this->localeArgument, $language);

        return $handler->handle($request);
    }

    /**
     * Return new instance specifying the default language.
     */
    public function withDefaultLanguage(string $defaultLanguage): self
    {
        $new = clone $this;
        $new->defaultLanguage = $defaultLanguage;

        return $new;
    }

    /**
     * Return new instance specifying the name of the argument that has the language.
     */
    public function withLocaleArgument(string $localeArgument): self
    {
        $new = clone $this;
        $new->localeArgument = $localeArgument;

        return $new;
    }

    /**
     * Gets the client's preferred language from the Accept-Language header.
     */
    private function getLanguage(string $path): string
    {
        $language = '';

        if ($path !== '') {
            $language = strtolower(explode('/', $path)[1]);
        }

        if (in_array($language, $this->languages, true)) {
            return $language;
        }

        if ($this->isValidLanguage($language)) {
            return $language;
        }

        return $this->defaultLanguage;
    }

    /**
     * Returns the URL path with the language code.
     */
    private function getUrlPathWithLanguage(string $path, string $language): string
    {
        if (str_contains($path, "/$language")) {
            return $path;
        }

        return '/' . $language . $path;
    }

    /**
     * Checks if a language code is valid (for example has the format xx or xx-xx).
     */
    private function isValidLanguage(string $language): bool
    {
        return preg_match('/[a-z]{2}(?:-[a-z]{2})?/', $language) && $this->isValidLanguageCode($language);
    }

    /**
     * Sets the application language based on the client's preferred language.
     */
    private function setLocale(string $language): void
    {
        $this->translator->setLocale($language);
        $this->urlGenerator->setDefaultArgument($this->localeArgument, $language);
    }

    /**
     * Determines if a URL should be ignored by the middleware.
     */
    private function shouldIgnoreUrl(ServerRequestInterface $request): bool
    {
        $currentUrl = $request->getUri()->getPath();

        return in_array($currentUrl, $this->ignoredUrls, true);
    }

    private function isValidLanguageCode(string $languageCode): bool
    {
        $validLanguageCodes = [
            'aa', 'ab', 'af', 'ak', 'am', 'ar', 'as', 'ay', 'az', 'ba', 'be', 'bg', 'bh', 'bi', 'bn', 'bo', 'br', 'bs',
            'ca', 'ce', 'ch', 'co', 'cs', 'cu', 'cv', 'cy', 'da', 'de', 'dv', 'dz', 'el', 'en', 'eo', 'es', 'et', 'eu',
            'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy', 'ga', 'gd', 'gl', 'gn', 'gu', 'gv', 'ha', 'he', 'hi', 'ho', 'hr',
            'ht', 'hu', 'hy', 'hz', 'ia', 'id', 'ie', 'ig', 'ii', 'ik', 'io', 'is', 'it', 'iu', 'ja', 'jv', 'ka', 'kg',
            'ki', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'kr', 'ks', 'ku', 'kv', 'kw', 'ky', 'la', 'lb', 'lg', 'li', 'ln',
            'lo', 'lt', 'lu', 'lv', 'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mo', 'mr', 'ms', 'mt', 'my', 'na', 'nb', 'nd',
            'ne', 'ng', 'nl', 'nn', 'no', 'nr', 'nv', 'ny', 'oc', 'oj', 'om', 'or', 'os', 'pa', 'pi', 'pl', 'ps', 'pt',
            'qu', 'rm', 'rn', 'ro', 'ru', 'rw', 'sa', 'sc', 'sd', 'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn', 'so', 'sq',
            'sr', 'ss', 'st', 'su', 'sv', 'sw', 'ta', 'te', 'tg', 'th', 'ti', 'tk', 'tl', 'tn', 'to', 'tr', 'ts', 'tt',
            'tw', 'ty', 'ug', 'uk', 'ur', 'uz', 've', 'vi', 'vo', 'wa', 'wo', 'xh', 'yi', 'yo', 'za', 'zh', 'zu'
        ];

        return in_array($languageCode, $validLanguageCodes);
    }
}
