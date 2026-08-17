<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Dto\Usuario\DataResponse;

class InicioUsuarioDto
{
    public function __construct(public ?string $message,
                                public ?string $token)
    {

    }
}