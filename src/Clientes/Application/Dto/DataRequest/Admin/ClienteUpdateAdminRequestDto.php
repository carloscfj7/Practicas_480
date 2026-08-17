<?php

declare(strict_types=1);

namespace App\Clientes\Application\Dto\DataRequest\Admin;

final readonly class ClienteUpdateAdminRequestDto
{
    public function __construct(
        public ?string $email = null,
        public ?string $contacto = null,
        public ?string $direccion = null
    )
    {
    }
}