<?php

declare(strict_types=1);

namespace Yii\Middleware\Tests\Locale;

use PHPUnit\Framework\TestCase;
use Yii\Middleware\Locale;
use Yii\Middleware\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testImmutable(): void
    {
        $this->createContainer();

        $locale = new Locale($this->localeRouteHelper, $this->translator, $this->urlGenerator, [], []);
        $this->assertNotSame($locale, $locale->withDefaultLanguage('en'));
        $this->assertNotSame($locale, $locale->withLocaleArgument('_language'));
    }
}
