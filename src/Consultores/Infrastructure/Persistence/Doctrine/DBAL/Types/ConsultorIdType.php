<?php

declare(strict_types=1);

namespace App\Consultores\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Consultores\Domain\ValueObjects\ConsultorId;
use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;

class ConsultorIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return ConsultorId::class;
    }

    public static function customTypeName(): string
    {
        return 'ConsultorId';
    }
}
