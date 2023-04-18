<?php

declare(strict_types=1);

use Yii\Middleware\Guest;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\User\CurrentUser;

return [
    Guest::class => [
        '__construct()' => [
            'isGuest' => DynamicReference::to(static fn (CurrentUser $currentUser): bool => $currentUser->isGuest()),
        ],
    ],
];
