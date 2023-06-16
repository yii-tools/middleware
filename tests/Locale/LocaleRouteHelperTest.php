<?php

declare(strict_types=1);

namespace Yii\Middleware\Tests\Locale;

use PHPUnit\Framework\TestCase;
use Yii\Middleware\LocaleRouteHelper;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class LocaleRouteHelperTest extends TestCase
{
    public function testLocaleRouteHelper(): void
    {
        $localeRouteHelper = new LocaleRouteHelper();
        $localeRouteHelper->setPath('/contact');

        $this->assertSame('/contact', $localeRouteHelper->getPath());
    }
}
