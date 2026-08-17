<?php

declare(strict_types=1);

namespace App\Consultores\Infrastructure\Persistence\Doctrine\DBAL\Types;
use App\Consultores\Domain\ValueObjects\DisponibilidadId;
use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;

class DisponibilidadIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return DisponibilidadId::class;
    }

    public static function customTypeName(): string
    {
        return 'DisonibilidadId';
    }
}
