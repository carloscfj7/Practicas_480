<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Shared\Domain\Model\UserId;

final class UserIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return UserId::class;
    }

    public static function customTypeName(): string
    {
        return 'UserId';
    }
}