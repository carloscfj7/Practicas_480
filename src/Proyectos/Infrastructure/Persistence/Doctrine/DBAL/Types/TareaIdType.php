<?php

declare(strict_types=1);

namespace App\Proyectos\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Proyectos\Domain\ValueObjects\TareaId;
use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;

class TareaIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return TareaId::class;
    }

    public static function customTypeName(): string
    {
        return 'TareaId';
    }
}
