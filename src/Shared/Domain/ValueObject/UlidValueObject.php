<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Symfony\Component\Uid\Ulid;

class UlidValueObject extends Ulid
{
    public function value(): string
    {
        return $this->uid;
    }
}