<?php

declare(strict_types=1);

namespace App\Clientes\Application\Dto\DataResponse;

class ClienteCreateResponseDto
{
    public function __construct(
        public string $message,
        public string $email
    )
    {
    }
}