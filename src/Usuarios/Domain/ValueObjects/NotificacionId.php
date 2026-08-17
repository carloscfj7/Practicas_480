<?php

declare(strict_types=1);

namespace App\Usuarios\Domain\ValueObjects;

use App\Shared\Domain\ValueObject\UlidValueObject;
use Symfony\Component\Uid\Ulid;

class NotificacionId extends UlidValueObject
{
    public static function create(): self
    {
        return new self(Ulid::generate());
    }
}