<?php

declare(strict_types=1);

namespace App\Proyectos\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Proyectos\Domain\ValueObjects\ActividadId;
use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;

class ActividadIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return ActividadId::class;
    }

    public static function customTypeName(): string
    {
        return 'ActividadId';
    }
}
