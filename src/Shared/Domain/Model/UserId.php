<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use App\Shared\Domain\ValueObject\UlidValueObject;
use Symfony\Component\Uid\Ulid;

class UserId extends UlidValueObject
{
    public static function create(): self
    {
        return new self(Ulid::generate());
    }
}