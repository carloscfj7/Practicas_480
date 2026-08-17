<?php
declare(strict_types=1);


namespace App\Consultores\Domain\ValueObjects;

use App\Consultores\Domain\Exceptions\Consultor\InvalidPerfilException;
use App\Consultores\Domain\Exceptions\Habilidad\InvalidNivelException;

enum Nivel:string
{
    case ALTO = 'alto';
    case BAJO = 'bajo';
    case MEDIO = 'medio';
    case EXPERTO = 'experto';

    public function label(): string
    {
        return match($this) {
            self::ALTO => 'Alto',
            self::BAJO => 'Bajo',
            self::MEDIO => 'Medio',
            self::EXPERTO => 'Experto',
        };
    }

    public static function fromString(string $value): self
    {
        foreach (self::cases() as $nivel) {
            if ($nivel->value === $value) {
                return $nivel;
            }
        }

        throw new InvalidNivelException();
    }
}
