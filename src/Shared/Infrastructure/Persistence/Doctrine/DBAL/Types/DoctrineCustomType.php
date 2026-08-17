<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

interface DoctrineCustomType
{
    public static function customTypeName(): string;
}
