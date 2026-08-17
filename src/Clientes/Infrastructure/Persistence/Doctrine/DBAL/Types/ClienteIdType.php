<?php

declare(strict_types=1);

namespace App\Clientes\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Clientes\Domain\ValueObjects\ClienteId;
use App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UlidType;

final class ClienteIdType extends UlidType
{
    protected function typeClassName(): string
    {
        return ClienteId::class;
    }

    public static function customTypeName(): string
    {
        return 'ClienteId';
    }
}
