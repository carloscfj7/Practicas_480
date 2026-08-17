<?php

declare(strict_types=1);

namespace App\Proyectos\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Proyectos\Domain\ValueObjects\ProyectoId;
use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;

class ProyectoIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return ProyectoId::class;
    }

    public static function customTypeName(): string
    {
        return 'ProyectoId';
    }
}
