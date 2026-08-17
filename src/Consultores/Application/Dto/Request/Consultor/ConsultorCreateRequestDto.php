<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Request\Consultor;

use App\Consultores\Domain\ValueObjects\Perfil;

class ConsultorCreateRequestDto
{
    public function __construct(
        public ?string $email,
        public ?string $password,
        public ?string $nombre,
        public ?string $apellidos,
        public ?Perfil $perfil,
        public ?array $habilidades
    )
    {
    }
}