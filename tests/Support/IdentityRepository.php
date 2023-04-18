<?php

declare(strict_types=1);

namespace Yii\Middleware\Tests\Support;

use Exception;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

final class IdentityRepository implements IdentityRepositoryInterface
{
    private bool $withException = false;

    public function __construct(private IdentityInterface|null $identity = null)
    {
    }

    public function findIdentity(string $id): IdentityInterface|null
    {
        if ($this->withException) {
            throw new Exception();
        }

        return $this->identity;
    }
}
