<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Usuarios\Application\Exceptions\Usuario\InvalidEmailException;
use InvalidArgumentException;

class EmailValueObject extends StringValueObject
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        $this->isValidEmail($value);
        parent::__construct($value);
    }

    /**
     * @throws InvalidEmailException
     */
    private function isValidEmail(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }
    }
}
