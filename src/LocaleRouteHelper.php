<?php

declare(strict_types=1);

namespace Yii\Middleware;

/**
 * Allows to get the path of the current request without locale part for default language.
 */
final class LocaleRouteHelper
{
    private string $path = '';

    /**
     * Sets the path of the current request without locale part for default language.
     *
     * @param string $path the path of the current request without locale part for default language.
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Returns the path of the current request without locale part for default language.
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
