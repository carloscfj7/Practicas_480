<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Dto\Usuario\Entity;

use App\Usuarios\Domain\Usuario;

class UsuarioDto
{
    private function __construct(public string $email)
    {
    }


    public static function fromEntity(Usuario $usuario): self
    {
        return new self($usuario->getEmail()->value());
    }
}