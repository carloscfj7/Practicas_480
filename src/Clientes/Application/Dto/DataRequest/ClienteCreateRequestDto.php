<?php

declare(strict_types=1);

namespace App\Clientes\Application\Dto\DataRequest;

class ClienteCreateRequestDto
{
    public function __construct(
        public ?string $email,
        public ?string $password,
        public ?string $nombre,
        public ?string $contacto,
        public ?string $direccion
    )
    {
    }
}