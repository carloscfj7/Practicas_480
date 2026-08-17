<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Response\Consultor;

class ConsultorCreateResponseDto
{
    public function __construct(
        public string $message,
        public string $email
    )
    {
    }
}