<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Request\Consultor;

class ConsultorUpdateRequestDto
{
    public function __construct(public ?string $perfil = null,
                                public ?array  $habilidades = [],
                                public ?array  $borrar_habilidades = [])
    {

    }
}