<?php

declare(strict_types=1);

use Yii\Middleware\Locale;

/** @var array $params */

return [
    Locale::class => [
        '__construct()' => [
            'languages' => $params['yii-tools/middleware']['locale']['languages'],
            'ignoredUrls' => $params['yii-tools/middleware']['locale']['ignoredUrls'],
        ],
    ],
];
