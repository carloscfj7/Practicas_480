<?php
declare(strict_types=1);

namespace App\Consultores\Domain\ValueObjects;


use App\Consultores\Domain\Exceptions\Consultor\InvalidPerfilException;

enum Perfil:string
{
    case PROJECT_MANAGER = 'project manager';
    case LIDER_TECNICO = 'líder técnico';
    case DESARROLLADOR = 'desarrollador';
    case DISEÑADOR = 'diseñador';

    public function label(): string
    {
        return match($this) {
            self::PROJECT_MANAGER => 'Project Manager',
            self::LIDER_TECNICO => 'Líder Técnico',
            self::DESARROLLADOR => 'Desarrollador',
            self::DISEÑADOR => 'Diseñador',

        };
    }

    public static function fromString(string $value): self
    {
        foreach (self::cases() as $perfil) {
            if ($perfil->value === $value) {
                return $perfil;
            }
        }

        throw new InvalidPerfilException();
    }

}
