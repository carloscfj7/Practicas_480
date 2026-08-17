<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Dto\Usuario\DataResponse;

class RegistroUsuarioDto
{
    public function __construct(public ?string $message, public ?string $email)
    {

    }
}