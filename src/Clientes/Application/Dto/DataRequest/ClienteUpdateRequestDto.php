<?php

declare(strict_types=1);

namespace App\Clientes\Application\Dto\DataRequest;

final readonly class ClienteUpdateRequestDto
{
    public function __construct(
        public ?string $contacto = null,
        public ?string $direccion = null
    )
    {
    }
}