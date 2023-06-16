<?php

declare(strict_types=1);

namespace Yii\Middleware;

/**
 * Allows to get the path of the current request without locale part for default language.
 */
final class LocaleRouteHelper
{
    public function __construct(private readonly string $path)
    {
    }

    /**
     * Returns the path of the current request without locale part for default language.
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
