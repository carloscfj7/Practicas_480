<?php

declare(strict_types=1);

namespace App\Usuarios\Infrastructure\Persisitence\Doctrine\DBAL\Types;

use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;
use App\Usuarios\Domain\ValueObjects\NotificacionId;

class NotificacionIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return NotificacionId::class;
    }

    public static function customTypeName(): string
    {
        return 'NotificacionId';
    }
}
