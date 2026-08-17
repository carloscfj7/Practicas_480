<?php

declare(strict_types=1);

namespace App\Consultores\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Consultores\Domain\ValueObjects\HabilidadId;
use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;

class HabilidadIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return HabilidadId::class;
    }

    public static function customTypeName(): string
    {
        return 'HabilidadId';
    }
}
