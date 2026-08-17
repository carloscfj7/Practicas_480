<?php

declare(strict_types=1);

namespace App\Shared\Application\Dto\Response;

class UpdateServicesResponseDto
{
    public function __construct(
        public ?string $message = null,
        public ?array $actualizacion = [],
    )
    {
    }
}