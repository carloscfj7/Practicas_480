<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Usuarios\Domain\ValueObjects\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EmailType extends CustomStringType
{
    protected function typeClassName(): string
    {
        return Email::class;
    }

    public static function customTypeName(): string
    {
        return 'Email';
    }

    /**
     * @param Email $value
     *
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    }
}
