<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

abstract class UlidType extends StringType implements DoctrineCustomType
{
    abstract protected function typeClassName(): string;

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return null;
        }

        $className = $this->typeClassName();

        return new $className($value);
    }

    public function getName(): string
    {
        return static::customTypeName();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}

